<?php

namespace App\Services;

use App\Models\KvcMeeting;
use App\Models\KvcMeetingParticipant;
use App\Models\KvcMeetingParticipantValue;
use App\Models\KvcMeetingSection;
use App\Models\KvcNode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KvcService
{
    public function getTree(): array
    {
        $this->ensureRootExists();

        $nodes = KvcNode::query()
            ->with(['owner', 'users', 'meetingManagers'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $byParent = $nodes->groupBy(fn (KvcNode $node) => $node->parent_id ?: '__root__');
        $root = $nodes->firstWhere('parent_id', null) ?? $nodes->first();

        if (! $root) {
            return [];
        }

        $build = function (KvcNode $node) use (&$build, $byParent): array {
            $children = ($byParent[$node->id] ?? collect())
                ->sortBy(['sort_order', 'id'])
                ->values()
                ->map(fn (KvcNode $child) => $build($child))
                ->all();

            return $this->nodeToArray($node, $children);
        };

        return $build($root);
    }

    public function updateTree(User $actor, array $tree): array
    {
        $this->assertAdmin($actor);

        if (empty($tree['id'])) {
            throw ValidationException::withMessages(['tree' => 'Не указан корневой КВЦ.']);
        }

        DB::transaction(function () use ($tree): void {
            $seen = [];
            $this->syncNodeRecursive($tree, null, 0, $seen);

            KvcNode::query()
                ->whereNotIn('id', array_keys($seen))
                ->delete();
        });

        return $this->getTree();
    }

    public function updateNodeValue(User $actor, string $nodeId, mixed $value): array
    {
        DB::transaction(function () use ($actor, $nodeId, $value): void {
            $node = KvcNode::query()->lockForUpdate()->findOrFail($nodeId);

            if (! $actor->isAdmin() && (int) $node->owner_user_id !== (int) $actor->id) {
                throw new AuthorizationException('Текущее значение может изменять только владелец КВЦ / ОП.');
            }

            $node->current_value = is_scalar($value) || $value === null ? (string) ($value ?? '') : json_encode($value, JSON_UNESCAPED_UNICODE);
            $node->last_value_date = now()->toDateString();
            $node->save();
        });

        return $this->getTree();
    }

    /** @return array<int, array{id:string,fio:string}> */
    public function users(): array
    {
        return User::query()->get()
            ->sortBy(fn (User $user) => mb_strtolower($user->displayName()))
            ->values()
            ->map(fn (User $user) => [
                'id' => (string) $user->id,
                'fio' => $user->displayName(),
            ])
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    public function getMeetings(User $actor, string $nodeId): array
    {
        KvcNode::query()->findOrFail($nodeId);

        $meetings = $this->loadMeetings($nodeId);

        return $this->serializeMeetings($meetings);
    }

    public function saveMeeting(User $actor, string $nodeId, array $payload): array
    {
        $node = KvcNode::query()->findOrFail($nodeId);
        $this->assertCanManageMeetings($actor, $node);

        $meetingId = trim((string) ($payload['id'] ?? ''));
        if ($meetingId === '') {
            $meetingId = 'meeting-'.now()->getTimestampMs().'-'.random_int(100000, 999999);
        }

        DB::transaction(function () use ($actor, $node, $nodeId, $meetingId, $payload): void {
            /** @var KvcMeeting|null $meeting */
            $meeting = KvcMeeting::query()->lockForUpdate()->find($meetingId);
            $isNew = $meeting === null;
            $wasClosed = (bool) ($meeting?->closed ?? false);

            if ($meeting && $meeting->kvc_node_id !== $nodeId) {
                throw ValidationException::withMessages(['meeting' => 'Собрание относится к другому КВЦ.']);
            }

            if ($wasClosed) {
                throw ValidationException::withMessages(['meeting' => 'Закрытое собрание нельзя редактировать.']);
            }

            $dateTime = $meeting?->date_time;
            if ($isNew) {
                $rawDate = trim((string) ($payload['dateTime'] ?? ''));
                if ($rawDate === '') {
                    throw ValidationException::withMessages(['dateTime' => 'Укажите дату собрания.']);
                }
                $dateTime = Carbon::parse($rawDate);
            }

            if (! $meeting) {
                $meeting = new KvcMeeting([
                    'id' => $meetingId,
                    'kvc_node_id' => $nodeId,
                    'date_time' => $dateTime,
                    'created_by_user_id' => $actor->id,
                    'created_by_name' => $actor->displayName(),
                ]);
            }

            $meeting->summary = (string) ($payload['summary'] ?? '');
            $meeting->updated_by_user_id = $actor->id;
            $meeting->updated_by_name = $actor->displayName();
            $meeting->save();

            if ($isNew) {
                $this->syncNewMeetingSectionsAndParticipants($meeting, $node, $payload);
            } else {
                $this->updateMeetingParticipantManagerFields($meeting, $payload);
            }

            $changes = is_array($payload['__participantFieldChanges'] ?? null)
                ? $payload['__participantFieldChanges']
                : [];
            $this->applyManagerParticipantFieldChanges($actor, $meeting, $changes);

            $wantsClosed = (bool) ($payload['closed'] ?? false);
            if ($wantsClosed) {
                $this->validateMeetingCanClose($meeting);
                $meeting->closed = true;
                $meeting->closed_at = ! empty($payload['closedAt']) ? Carbon::parse($payload['closedAt']) : now();
                $meeting->closed_by_user_id = $actor->id;
                $meeting->closed_by_name = $actor->displayName();
            } else {
                $meeting->closed = false;
            }

            $meeting->save();
        });

        $meetings = $this->serializeMeetings($this->loadMeetings($nodeId));

        foreach ($meetings as $meeting) {
            if ((string) $meeting['id'] === $meetingId) {
                return $meeting;
            }
        }

        throw ValidationException::withMessages(['meeting' => 'Собрание не найдено после сохранения.']);
    }

    public function updateOwnMeetingParticipant(
        User $actor,
        string $nodeId,
        string $meetingId,
        string $participantUserId,
        string $participantUserName,
        array $data,
        ?string $sectionNodeId = null,
    ): array {
        DB::transaction(function () use (
            $actor,
            $nodeId,
            $meetingId,
            $participantUserId,
            $participantUserName,
            $data,
            $sectionNodeId,
        ): void {
            $meeting = KvcMeeting::query()
                ->where('kvc_node_id', $nodeId)
                ->lockForUpdate()
                ->findOrFail($meetingId);

            $sectionId = $sectionNodeId ?: $nodeId;
            $participant = $this->findParticipant($meeting, $sectionId, $participantUserId, $participantUserName, true);

            if (! $participant) {
                throw ValidationException::withMessages(['participant' => 'Участник собрания не найден.']);
            }

            if ((int) $participant->user_id !== (int) $actor->id) {
                throw new AuthorizationException('Можно редактировать только свои обязательства.');
            }

            $hasOutcomeChange = array_key_exists('currentResult', $data) || array_key_exists('currentComment', $data);
            if ($hasOutcomeChange && $this->isOutcomeLocked($meeting, $sectionId)) {
                throw ValidationException::withMessages([
                    'participant' => 'Итог и комментарий нельзя изменить: следующее собрание уже закрыто.',
                ]);
            }

            $value = $this->lockParticipantValue($participant, $actor);

            if (! $meeting->closed && array_key_exists('currentObligation', $data)) {
                $value->current_obligation = (string) ($data['currentObligation'] ?? '');
            }

            if (array_key_exists('currentResult', $data)) {
                $value->current_result = (string) ($data['currentResult'] ?? '');
            }

            if (array_key_exists('currentComment', $data)) {
                $value->current_comment = (string) ($data['currentComment'] ?? '');
            }

            $value->version = ((int) $value->version) + 1;
            $value->updated_by_user_id = $actor->id;
            $value->updated_by_name = $actor->displayName();
            $value->save();
        });

        $meetings = $this->serializeMeetings($this->loadMeetings($nodeId));
        foreach ($meetings as $meeting) {
            if ((string) $meeting['id'] === $meetingId) {
                return $meeting;
            }
        }

        throw ValidationException::withMessages(['meeting' => 'Собрание не найдено.']);
    }

    public function deleteMeetingParticipant(
        User $actor,
        string $nodeId,
        string $meetingId,
        string $participantUserId,
        string $participantUserName,
        ?string $sectionNodeId = null,
    ): array {
        $this->assertAdmin($actor, 'Удалять пользователей из собрания может только администратор системы.');

        DB::transaction(function () use ($nodeId, $meetingId, $participantUserId, $participantUserName, $sectionNodeId): void {
            $meeting = KvcMeeting::query()
                ->where('kvc_node_id', $nodeId)
                ->lockForUpdate()
                ->findOrFail($meetingId);

            $participant = $this->findParticipant(
                $meeting,
                $sectionNodeId ?: $nodeId,
                $participantUserId,
                $participantUserName,
                true,
            );

            if (! $participant) {
                throw ValidationException::withMessages(['participant' => 'Участник собрания не найден.']);
            }

            $participant->delete();
        });

        $meetings = $this->serializeMeetings($this->loadMeetings($nodeId));
        foreach ($meetings as $meeting) {
            if ((string) $meeting['id'] === $meetingId) {
                return $meeting;
            }
        }

        throw ValidationException::withMessages(['meeting' => 'Собрание не найдено.']);
    }

    public function deleteMeeting(User $actor, string $nodeId, string $meetingId): bool
    {
        $node = KvcNode::query()->findOrFail($nodeId);
        $this->assertCanManageMeetings($actor, $node);

        return DB::transaction(function () use ($nodeId, $meetingId): bool {
            $meeting = KvcMeeting::query()
                ->where('kvc_node_id', $nodeId)
                ->lockForUpdate()
                ->find($meetingId);

            if (! $meeting) {
                throw ValidationException::withMessages(['meeting' => 'Собрание не найдено.']);
            }

            $meeting->delete();

            return true;
        });
    }

    private function ensureRootExists(): void
    {
        if (KvcNode::query()->exists()) {
            return;
        }

        KvcNode::query()->create([
            'id' => 'kvc-root',
            'parent_id' => null,
            'type' => 'kvc',
            'type_name' => 'КВЦ',
            'description' => 'КВЦ Компании',
            'indicator_type' => 'percent',
            'range_from' => 0,
            'range_to' => 100,
            'true_label' => 'Да',
            'false_label' => 'Нет',
            'current_value' => '',
            'autoaudit' => '—',
            'sort_order' => 0,
        ]);
    }

    /** @param array<string, bool> $seen */
    private function syncNodeRecursive(array $data, ?string $parentId, int $sortOrder, array &$seen): void
    {
        $id = trim((string) ($data['id'] ?? ''));
        if ($id === '') {
            throw ValidationException::withMessages(['tree' => 'У одного из элементов КВЦ отсутствует ID.']);
        }

        if (isset($seen[$id])) {
            throw ValidationException::withMessages(['tree' => 'В дереве найден повторяющийся ID: '.$id]);
        }
        $seen[$id] = true;

        $type = (string) ($data['type'] ?? 'kvc-op');
        if (! in_array($type, ['kvc', 'kvc-op', 'op'], true)) {
            $type = 'kvc-op';
        }

        $ownerId = $this->normalizeUserId($data['ownerId'] ?? null)
            ?? $this->findUserIdByDisplayName((string) ($data['owner'] ?? ''));
        $owner = $ownerId ? User::query()->find($ownerId) : null;

        KvcNode::query()->updateOrCreate(
            ['id' => $id],
            [
                'parent_id' => $parentId,
                'type' => $type,
                'type_name' => (string) ($data['typeName'] ?? ($type === 'op' ? 'ОП' : 'ОП / КВЦ')),
                'title' => (string) ($data['title'] ?? ''),
                'description' => (string) ($data['description'] ?? ''),
                'additional' => (string) ($data['additional'] ?? ''),
                'indicator_type' => (string) ($data['indicatorType'] ?? 'percent'),
                'range_from' => $this->nullableNumber($data['rangeFrom'] ?? null),
                'range_to' => $this->nullableNumber($data['rangeTo'] ?? null),
                'true_label' => (string) ($data['trueLabel'] ?? 'Да'),
                'false_label' => (string) ($data['falseLabel'] ?? 'Нет'),
                // current_value и last_value_date намеренно не перезаписываются целым деревом.
                'autoaudit' => (string) ($data['autoaudit'] ?? '—'),
                'owner_user_id' => $owner?->id,
                'owner_name' => $owner?->displayName() ?: (string) ($data['owner'] ?? ''),
                'start_date' => $this->nullableDate($data['startDate'] ?? null),
                'end_date' => $this->nullableDate($data['endDate'] ?? null),
                'sort_order' => $sortOrder,
            ],
        );

        $node = KvcNode::query()->findOrFail($id);

        $managerIds = collect($data['meetingManagerIds'] ?? [])
            ->map(fn ($value) => $this->normalizeUserId($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $node->meetingManagers()->sync($managerIds);

        $userIds = collect($data['users'] ?? [])
            ->map(function ($value) {
                if (is_array($value)) {
                    return $this->normalizeUserId($value['id'] ?? null)
                        ?? $this->findUserIdByDisplayName((string) ($value['fio'] ?? $value['name'] ?? ''));
                }
                if (is_object($value)) {
                    return $this->normalizeUserId($value->id ?? null)
                        ?? $this->findUserIdByDisplayName((string) ($value->fio ?? $value->name ?? ''));
                }
                if (is_string($value)) {
                    return $this->findUserIdByDisplayName($value);
                }
                return null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
        $node->users()->sync($userIds);

        $children = is_array($data['children'] ?? null) ? $data['children'] : [];
        if ($type === 'op') {
            $children = [];
        }

        foreach (array_values($children) as $index => $child) {
            if (is_array($child)) {
                $this->syncNodeRecursive($child, $id, $index, $seen);
            }
        }
    }

    /** @param array<int, array<string, mixed>> $children */
    private function nodeToArray(KvcNode $node, array $children): array
    {
        return [
            'id' => (string) $node->id,
            'type' => $node->type,
            'typeName' => $node->type_name,
            'title' => $node->title ?? '',
            'description' => $node->description ?? '',
            'additional' => $node->additional ?? '',
            'indicatorType' => $node->indicator_type,
            'rangeFrom' => $node->range_from !== null ? (float) $node->range_from : null,
            'rangeTo' => $node->range_to !== null ? (float) $node->range_to : null,
            'trueLabel' => $node->true_label ?: 'Да',
            'falseLabel' => $node->false_label ?: 'Нет',
            'currentValue' => $node->current_value ?? '',
            'autoaudit' => $node->autoaudit ?? '—',
            'ownerId' => $node->owner_user_id !== null ? (string) $node->owner_user_id : '',
            'owner' => $node->owner?->displayName() ?: ($node->owner_name ?? ''),
            'startDate' => $node->start_date?->format('Y-m-d') ?? '',
            'endDate' => $node->end_date?->format('Y-m-d') ?? '',
            'lastValueDate' => $node->last_value_date?->format('Y-m-d') ?? '',
            'meetingManagerIds' => $node->meetingManagers->pluck('id')->map(fn ($id) => (string) $id)->values()->all(),
            'users' => $node->users->map(fn (User $user) => [
                'id' => (string) $user->id,
                'fio' => $user->displayName(),
            ])->values()->all(),
            'children' => $children,
        ];
    }

    private function syncNewMeetingSectionsAndParticipants(KvcMeeting $meeting, KvcNode $mainNode, array $payload): void
    {
        $sections = $this->normalizePayloadSections($mainNode, $payload);

        foreach ($sections as $index => $section) {
            KvcMeetingSection::query()->updateOrCreate(
                ['meeting_id' => $meeting->id, 'node_id' => $section['nodeId']],
                ['node_name' => $section['nodeName'], 'sort_order' => $index],
            );
        }

        $allowedSectionIds = collect($sections)->pluck('nodeId')->map(fn ($v) => (string) $v)->all();
        $participants = is_array($payload['participants'] ?? null) ? $payload['participants'] : [];

        foreach ($participants as $item) {
            if (! is_array($item)) {
                continue;
            }

            $sectionId = (string) ($item['sectionNodeId'] ?? $mainNode->id);
            if (! in_array($sectionId, $allowedSectionIds, true)) {
                continue;
            }

            $userId = $this->normalizeUserId($item['userId'] ?? null);
            $userName = trim((string) ($item['userName'] ?? ''));
            if ($userId) {
                $user = User::query()->find($userId);
                $userName = $user?->displayName() ?: $userName;
            }
            if (! $userId && $userName === '') {
                continue;
            }

            // В собрании корневого КВЦ его владелец не участвует ни в одной секции.
            if ($mainNode->parent_id === null) {
                $rootOwnerId = $mainNode->owner_user_id ? (int) $mainNode->owner_user_id : null;
                $rootOwnerName = mb_strtolower(trim((string) ($mainNode->owner?->displayName() ?: $mainNode->owner_name ?: '')));
                $participantName = mb_strtolower(trim($userName));

                if (
                    ($rootOwnerId && $userId && $rootOwnerId === $userId)
                    || ($rootOwnerName !== '' && $participantName !== '' && $rootOwnerName === $participantName)
                ) {
                    continue;
                }
            }

            $participant = $this->firstOrCreateParticipant($meeting, $sectionId, $userId, $userName);
            $participant->previous_status = (string) ($item['previousStatus'] ?? '');
            $participant->attendance = (string) ($item['attendance'] ?? '');
            $participant->save();

            $value = KvcMeetingParticipantValue::query()->firstOrCreate(
                ['participant_id' => $participant->id],
                [
                    'current_obligation' => (string) ($item['currentObligation'] ?? ''),
                    'current_result' => (string) ($item['currentResult'] ?? ''),
                    'current_comment' => (string) ($item['currentComment'] ?? ''),
                    'version' => 1,
                ],
            );

            // При первом создании значения берём из формы. Повторное сохранение не затирает их целым объектом собрания.
            if (! $value->wasRecentlyCreated) {
                // Ничего: персональные поля обновляются только через явный список изменений.
            }
        }
    }

    private function updateMeetingParticipantManagerFields(KvcMeeting $meeting, array $payload): void
    {
        $participants = is_array($payload['participants'] ?? null) ? $payload['participants'] : [];

        foreach ($participants as $item) {
            if (! is_array($item)) {
                continue;
            }

            $sectionId = (string) ($item['sectionNodeId'] ?? $meeting->kvc_node_id);
            $userId = $this->normalizeUserId($item['userId'] ?? null);
            $userName = (string) ($item['userName'] ?? '');

            $participant = $this->findParticipant($meeting, $sectionId, (string) ($userId ?: ''), $userName, true);
            if (! $participant) {
                $participant = $this->firstOrCreateParticipant($meeting, $sectionId, $userId, $userName);
            }

            $participant->previous_status = (string) ($item['previousStatus'] ?? '');
            $participant->attendance = (string) ($item['attendance'] ?? '');
            $participant->save();
        }
    }

    private function applyManagerParticipantFieldChanges(User $actor, KvcMeeting $meeting, array $changes): void
    {
        foreach ($changes as $change) {
            if (! is_array($change)) {
                continue;
            }

            $sectionId = (string) ($change['sectionNodeId'] ?? $meeting->kvc_node_id);
            $userId = (string) ($change['userId'] ?? '');
            $userName = (string) ($change['userName'] ?? '');
            $data = is_array($change['data'] ?? null) ? $change['data'] : [];

            $participant = $this->findParticipant($meeting, $sectionId, $userId, $userName, true);
            if (! $participant) {
                throw ValidationException::withMessages(['participant' => 'Участник собрания не найден: '.($userName ?: $userId)]);
            }

            if (array_key_exists('previousResult', $data) || array_key_exists('previousComment', $data)) {
                $previous = $this->findPreviousMeeting($meeting, $sectionId);
                if (! $previous) {
                    throw ValidationException::withMessages(['participant' => 'Не найдено предыдущее собрание для сохранения итога и комментария.']);
                }

                $previousParticipant = $this->findParticipant(
                    $previous,
                    $sectionId,
                    (string) ($participant->user_id ?: ''),
                    $participant->user_name,
                    true,
                );
                if (! $previousParticipant) {
                    throw ValidationException::withMessages(['participant' => 'Участник предыдущего собрания не найден: '.$participant->user_name]);
                }

                $previousValue = $this->lockParticipantValue($previousParticipant, $actor);
                if (array_key_exists('previousResult', $data)) {
                    $previousValue->current_result = (string) ($data['previousResult'] ?? '');
                }
                if (array_key_exists('previousComment', $data)) {
                    $previousValue->current_comment = (string) ($data['previousComment'] ?? '');
                }
                $previousValue->version = ((int) $previousValue->version) + 1;
                $previousValue->updated_by_user_id = $actor->id;
                $previousValue->updated_by_name = $actor->displayName();
                $previousValue->save();
            }

            if (
                array_key_exists('currentObligation', $data)
                || array_key_exists('currentResult', $data)
                || array_key_exists('currentComment', $data)
            ) {
                $value = $this->lockParticipantValue($participant, $actor);
                if (array_key_exists('currentObligation', $data)) {
                    $value->current_obligation = (string) ($data['currentObligation'] ?? '');
                }
                if (array_key_exists('currentResult', $data)) {
                    $value->current_result = (string) ($data['currentResult'] ?? '');
                }
                if (array_key_exists('currentComment', $data)) {
                    $value->current_comment = (string) ($data['currentComment'] ?? '');
                }
                $value->version = ((int) $value->version) + 1;
                $value->updated_by_user_id = $actor->id;
                $value->updated_by_name = $actor->displayName();
                $value->save();
            }
        }
    }

    private function validateMeetingCanClose(KvcMeeting $meeting): void
    {
        $participants = KvcMeetingParticipant::query()->where('meeting_id', $meeting->id)->get();
        foreach ($participants as $participant) {
            if (trim((string) $participant->previous_status) === '' || trim((string) $participant->attendance) === '') {
                throw ValidationException::withMessages(['meeting' => 'Не все статусы установлены.']);
            }
        }
    }

    /** @return array<int, array{nodeId:string,nodeName:string}> */
    private function normalizePayloadSections(KvcNode $mainNode, array $payload): array
    {
        $result = [[
            'nodeId' => (string) $mainNode->id,
            'nodeName' => $this->nodeDisplayName($mainNode),
        ]];
        $seen = [(string) $mainNode->id => true];

        $source = is_array($payload['kvcSections'] ?? null) ? $payload['kvcSections'] : [];
        foreach ($source as $section) {
            if (! is_array($section)) {
                continue;
            }
            $id = trim((string) ($section['nodeId'] ?? ''));
            if ($id === '' || isset($seen[$id]) || $id === (string) $mainNode->id) {
                continue;
            }

            $node = KvcNode::query()->find($id);
            if (! $node || $node->type === 'op' || ! $this->isDescendantOf($node, $mainNode)) {
                continue;
            }

            $seen[$id] = true;
            $result[] = [
                'nodeId' => $id,
                'nodeName' => $this->nodeDisplayName($node),
            ];
        }

        return $result;
    }

    private function isDescendantOf(KvcNode $node, KvcNode $ancestor): bool
    {
        $current = $node;
        $guard = 0;
        while ($current->parent_id && $guard++ < 100) {
            if ((string) $current->parent_id === (string) $ancestor->id) {
                return true;
            }
            $current = KvcNode::query()->find($current->parent_id);
            if (! $current) {
                return false;
            }
        }

        return false;
    }

    private function firstOrCreateParticipant(KvcMeeting $meeting, string $sectionId, ?int $userId, string $userName): KvcMeetingParticipant
    {
        $query = KvcMeetingParticipant::query()
            ->where('meeting_id', $meeting->id)
            ->where('section_node_id', $sectionId);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id')->where('user_name', $userName);
        }

        $participant = $query->first();
        if ($participant) {
            return $participant;
        }

        return KvcMeetingParticipant::query()->create([
            'meeting_id' => $meeting->id,
            'section_node_id' => $sectionId,
            'user_id' => $userId,
            'user_name' => $userName,
            'previous_status' => '',
            'attendance' => '',
        ]);
    }

    private function findParticipant(
        KvcMeeting $meeting,
        string $sectionId,
        string $userId,
        string $userName,
        bool $lock = false,
    ): ?KvcMeetingParticipant {
        $query = KvcMeetingParticipant::query()
            ->where('meeting_id', $meeting->id)
            ->where('section_node_id', $sectionId);

        if ($userId !== '') {
            $query->where('user_id', (int) $userId);
        } else {
            $query->where('user_name', $userName);
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    private function lockParticipantValue(KvcMeetingParticipant $participant, User $actor): KvcMeetingParticipantValue
    {
        KvcMeetingParticipantValue::query()->firstOrCreate(
            ['participant_id' => $participant->id],
            [
                'current_obligation' => '',
                'current_result' => '',
                'current_comment' => '',
                'version' => 1,
                'updated_by_user_id' => $actor->id,
                'updated_by_name' => $actor->displayName(),
            ],
        );

        return KvcMeetingParticipantValue::query()
            ->where('participant_id', $participant->id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function isOutcomeLocked(KvcMeeting $meeting, string $sectionId): bool
    {
        $next = KvcMeeting::query()
            ->where('kvc_node_id', $meeting->kvc_node_id)
            ->where('date_time', '>', $meeting->date_time)
            ->whereHas('sections', fn ($query) => $query->where('node_id', $sectionId))
            ->orderBy('date_time')
            ->first();

        return (bool) ($next?->closed ?? false);
    }

    private function findPreviousMeeting(KvcMeeting $meeting, string $sectionId): ?KvcMeeting
    {
        return KvcMeeting::query()
            ->where('kvc_node_id', $meeting->kvc_node_id)
            ->where('date_time', '<', $meeting->date_time)
            ->whereHas('sections', fn ($query) => $query->where('node_id', $sectionId))
            ->orderByDesc('date_time')
            ->first();
    }

    private function loadMeetings(string $nodeId)
    {
        return KvcMeeting::query()
            ->where('kvc_node_id', $nodeId)
            ->with(['sections', 'participants.value'])
            ->orderBy('date_time')
            ->get();
    }

    /** @return array<int, array<string, mixed>> */
    private function serializeMeetings($meetings): array
    {
        $chronological = $meetings->sortBy('date_time')->values();
        $result = [];

        foreach ($chronological as $meeting) {
            $sections = $meeting->sections->sortBy('sort_order')->values();
            if ($sections->isEmpty()) {
                $node = KvcNode::query()->find($meeting->kvc_node_id);
                $sections = collect([(object) [
                    'node_id' => $meeting->kvc_node_id,
                    'node_name' => $node ? $this->nodeDisplayName($node) : 'КВЦ',
                ]]);
            }

            $sectionPayload = [];
            $previousBySection = [];
            foreach ($sections as $section) {
                $previous = $chronological
                    ->filter(fn (KvcMeeting $candidate) => $candidate->date_time < $meeting->date_time)
                    ->filter(fn (KvcMeeting $candidate) => $candidate->sections->contains(fn ($s) => (string) $s->node_id === (string) $section->node_id))
                    ->sortByDesc('date_time')
                    ->first();

                $previousBySection[(string) $section->node_id] = $previous;
                $sectionPayload[] = [
                    'nodeId' => (string) $section->node_id,
                    'nodeName' => (string) $section->node_name,
                    'previousMeetingId' => $previous?->id,
                    'previousMeetingDate' => $previous?->date_time?->format('Y-m-d\TH:i') ?? '',
                ];
            }

            $participants = [];
            foreach ($meeting->participants as $participant) {
                $sectionId = (string) $participant->section_node_id;
                /** @var KvcMeeting|null $previousMeeting */
                $previousMeeting = $previousBySection[$sectionId] ?? null;
                $previousParticipant = null;
                if ($previousMeeting) {
                    $previousParticipant = $previousMeeting->participants->first(function (KvcMeetingParticipant $candidate) use ($participant, $sectionId): bool {
                        if ((string) $candidate->section_node_id !== $sectionId) {
                            return false;
                        }
                        if ($participant->user_id && $candidate->user_id) {
                            return (int) $participant->user_id === (int) $candidate->user_id;
                        }
                        return mb_strtolower(trim($participant->user_name)) === mb_strtolower(trim($candidate->user_name));
                    });
                }

                $value = $participant->value;
                $previousValue = $previousParticipant?->value;

                $sectionInfo = collect($sectionPayload)->firstWhere('nodeId', $sectionId);

                $participants[] = [
                    'sectionNodeId' => $sectionId,
                    'sectionNodeName' => is_array($sectionInfo) ? (string) ($sectionInfo['nodeName'] ?? 'КВЦ') : 'КВЦ',
                    'userId' => $participant->user_id !== null ? (string) $participant->user_id : '',
                    'userName' => $participant->user_name,
                    'previousMeetingId' => $previousMeeting?->id,
                    'previousMeetingDate' => $previousMeeting?->date_time?->format('Y-m-d\TH:i') ?? '',
                    'previousObligation' => $previousValue?->current_obligation ?? '',
                    'previousResult' => $previousValue?->current_result ?? '',
                    'previousStatus' => $participant->previous_status ?? '',
                    'previousComment' => $previousValue?->current_comment ?? '',
                    'currentObligation' => $value?->current_obligation ?? '',
                    'currentResult' => $value?->current_result ?? '',
                    'attendance' => $participant->attendance ?? '',
                    'currentComment' => $value?->current_comment ?? '',
                    'personalUpdatedAt' => $value?->updated_at?->toIso8601String(),
                    'personalUpdatedBy' => $value?->updated_by_user_id !== null ? (string) $value->updated_by_user_id : null,
                    'personalUpdatedByName' => $value?->updated_by_name ?? '',
                    'personalVersion' => (int) ($value?->version ?? 0),
                ];
            }

            $mainPrevious = $previousBySection[(string) $meeting->kvc_node_id] ?? null;
            $result[] = [
                'id' => (string) $meeting->id,
                'kvcId' => (string) $meeting->kvc_node_id,
                'kvcSections' => $sectionPayload,
                'dateTime' => $meeting->date_time?->format('Y-m-d\TH:i') ?? '',
                'summary' => $meeting->summary ?? '',
                'participants' => $participants,
                'previousMeetingId' => $mainPrevious?->id,
                'previousMeetingDate' => $mainPrevious?->date_time?->format('Y-m-d\TH:i') ?? '',
                'closed' => (bool) $meeting->closed,
                'closedAt' => $meeting->closed_at?->toIso8601String(),
                'closedBy' => $meeting->closed_by_user_id !== null ? (string) $meeting->closed_by_user_id : null,
                'closedByName' => $meeting->closed_by_name ?? '',
                'createdAt' => $meeting->created_at?->toIso8601String(),
                'updatedAt' => $meeting->updated_at?->toIso8601String(),
                'updatedBy' => $meeting->updated_by_user_id !== null ? (string) $meeting->updated_by_user_id : null,
                'updatedByName' => $meeting->updated_by_name ?? '',
            ];
        }

        return collect($result)
            ->sortByDesc(fn (array $meeting) => $meeting['dateTime'])
            ->values()
            ->all();
    }

    private function assertCanManageMeetings(User $actor, KvcNode $node): void
    {
        if ($node->type === 'op') {
            throw new AuthorizationException('Для ОП собрания недоступны.');
        }

        if ($actor->isAdmin() || (int) $node->owner_user_id === (int) $actor->id) {
            return;
        }

        if ($node->meetingManagers()->whereKey($actor->id)->exists()) {
            return;
        }

        throw new AuthorizationException('Нет прав на создание, редактирование или удаление собраний этого КВЦ.');
    }

    private function assertAdmin(User $actor, string $message = 'Изменять структуру и настройки КВЦ может только администратор системы.'): void
    {
        if (! $actor->isAdmin()) {
            throw new AuthorizationException($message);
        }
    }

    private function nodeDisplayName(KvcNode $node): string
    {
        return trim((string) ($node->description ?: $node->title ?: $node->type_name ?: 'КВЦ'));
    }

    private function normalizeUserId(mixed $value): ?int
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        $id = (int) $value;

        return $id > 0 && User::query()->whereKey($id)->exists() ? $id : null;
    }


    private function findUserIdByDisplayName(string $name): ?int
    {
        $needle = mb_strtolower(trim($name));
        if ($needle === '') {
            return null;
        }

        $user = User::query()->get()->first(
            fn (User $candidate) => mb_strtolower(trim($candidate->displayName())) === $needle
        );

        return $user ? (int) $user->id : null;
    }

    private function nullableNumber(mixed $value): ?float
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function nullableDate(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        return Carbon::parse($value)->toDateString();
    }
}
