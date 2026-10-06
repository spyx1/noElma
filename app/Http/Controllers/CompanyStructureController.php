<?php

namespace App\Http\Controllers;

use App\Models\CompanyStructureNode;
use App\Models\User;
use App\Services\CompanyStructureExcelExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyStructureController extends Controller
{
    public function index(Request $request): View
    {
        $nodes = CompanyStructureNode::query()
            ->whereNull('parent_id')
            ->with(['users', 'childrenRecursive'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $allNodes = CompanyStructureNode::query()
            ->orderByRaw('parent_id is not null')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'parent_id', 'title']);

        $nodeOptions = $this->flattenNodes($nodes);

        $users = User::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('middle_name')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->displayName(),
            ]);

        return view('company-structure.index', [
            'nodes' => $nodes,
            'allNodes' => $allNodes,
            'nodeOptions' => $nodeOptions,
            'users' => $users,
            'canEdit' => (bool) $request->user()?->isAdmin(),
        ]);
    }

    public function export(CompanyStructureExcelExporter $exporter): StreamedResponse
    {
        $nodes = CompanyStructureNode::query()
            ->whereNull('parent_id')
            ->with(['users', 'childrenRecursive'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $path = $exporter->export($nodes);

        $rootTitle = trim((string) ($nodes->first()?->title ?: 'компании'));
        $safeRootTitle = preg_replace('~[\\/:*?"<>|]+~u', ' ', $rootTitle) ?: 'компании';
        $safeRootTitle = trim(preg_replace('/\s+/u', ' ', $safeRootTitle) ?: 'компании');
        $safeRootTitle = function_exists('mb_substr')
            ? mb_substr($safeRootTitle, 0, 90)
            : substr($safeRootTitle, 0, 90);

        $filename = 'Структура_' . $safeRootTitle . '.xlsx';
        $encodedFilename = rawurlencode($filename);

        return new StreamedResponse(
            static function () use ($path): void {
                $handle = fopen($path, 'rb');

                if ($handle === false) {
                    throw new \RuntimeException('Не удалось открыть сформированный Excel-файл.');
                }

                try {
                    while (!feof($handle)) {
                        $chunk = fread($handle, 1024 * 1024);

                        if ($chunk === false) {
                            throw new \RuntimeException('Ошибка чтения сформированного Excel-файла.');
                        }

                        echo $chunk;
                    }
                } finally {
                    fclose($handle);
                    @unlink($path);
                }
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="company-structure.xlsx"; filename*=UTF-8\'\'' . $encodedFilename,
                'Content-Length' => (string) filesize($path),
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        $data = $this->validatedData($request);
        $userIds = $this->normalizedUserIds($data['user_ids'] ?? []);
        $allowsMultiple = $request->boolean('allows_multiple_users');

        $this->assertUserCountAllowed($allowsMultiple, $userIds);

        DB::transaction(function () use ($data, $userIds, $allowsMultiple): void {
            $parentId = $data['parent_id'] ?? null;
            $sortOrder = ((int) CompanyStructureNode::query()
                ->where('parent_id', $parentId)
                ->max('sort_order')) + 1;

            $node = CompanyStructureNode::query()->create([
                'parent_id' => $parentId,
                'title' => trim($data['title']),
                'allows_multiple_users' => $allowsMultiple,
                'sort_order' => $sortOrder,
            ]);

            $node->users()->sync($userIds);
        });

        return redirect()
            ->route('workspace.company-structure')
            ->with('status', 'Узел структуры добавлен.');
    }

    public function update(Request $request, CompanyStructureNode $node): RedirectResponse
    {
        $this->ensureAdmin($request);

        $data = $this->validatedData($request);
        $newParentId = $data['parent_id'] ?? null;
        $userIds = $this->normalizedUserIds($data['user_ids'] ?? []);
        $allowsMultiple = $request->boolean('allows_multiple_users');

        $this->assertUserCountAllowed($allowsMultiple, $userIds);
        $this->assertParentAllowed($node, $newParentId);

        DB::transaction(function () use ($node, $data, $newParentId, $userIds, $allowsMultiple): void {
            if ((string) ($node->parent_id ?? '') !== (string) ($newParentId ?? '')) {
                $node->sort_order = ((int) CompanyStructureNode::query()
                    ->where('parent_id', $newParentId)
                    ->max('sort_order')) + 1;
            }

            $node->parent_id = $newParentId;
            $node->title = trim($data['title']);
            $node->allows_multiple_users = $allowsMultiple;
            $node->save();

            $node->users()->sync($userIds);
        });

        return redirect()
            ->route('workspace.company-structure')
            ->with('status', 'Узел структуры обновлён.');
    }

    public function destroy(Request $request, CompanyStructureNode $node): RedirectResponse
    {
        $this->ensureAdmin($request);

        DB::transaction(function () use ($node): void {
            $node->delete();
        });

        return redirect()
            ->route('workspace.company-structure')
            ->with('status', 'Узел структуры и его дочерние узлы удалены.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:company_structure_nodes,id'],
            'allows_multiple_users' => ['nullable', 'boolean'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ], [
            'title.required' => 'Укажите название должности или узла.',
            'title.max' => 'Название не должно быть длиннее 255 символов.',
            'parent_id.exists' => 'Выбранный родительский узел не найден.',
            'user_ids.*.exists' => 'Один из выбранных пользователей не найден.',
        ]);
    }

    /** @param array<int|string, mixed> $userIds
     *  @return array<int, int>
     */
    private function normalizedUserIds(array $userIds): array
    {
        return array_values(array_unique(array_map('intval', $userIds)));
    }

    /** @param array<int, int> $userIds */
    private function assertUserCountAllowed(bool $allowsMultiple, array $userIds): void
    {
        if (!$allowsMultiple && count($userIds) > 1) {
            throw ValidationException::withMessages([
                'user_ids' => 'Для обычной должности можно выбрать только одного сотрудника. Включите «Групповая должность», чтобы назначить нескольких.',
            ]);
        }
    }

    private function assertParentAllowed(CompanyStructureNode $node, ?int $newParentId): void
    {
        if ($newParentId === null) {
            return;
        }

        if ($newParentId === $node->id) {
            throw ValidationException::withMessages([
                'parent_id' => 'Узел нельзя сделать дочерним самому себе.',
            ]);
        }

        $cursor = CompanyStructureNode::query()->find($newParentId);

        while ($cursor) {
            if ($cursor->id === $node->id) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Нельзя переместить узел внутрь одного из его дочерних узлов.',
                ]);
            }

            $cursor = $cursor->parent_id
                ? CompanyStructureNode::query()->find($cursor->parent_id)
                : null;
        }
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    /**
     * @param iterable<CompanyStructureNode> $nodes
     * @return array<int, array{id:int,title:string,depth:int,parent_id:int|null}>
     */
    private function flattenNodes(iterable $nodes, int $depth = 0): array
    {
        $result = [];

        foreach ($nodes as $node) {
            $result[] = [
                'id' => $node->id,
                'title' => $node->title,
                'depth' => $depth,
                'parent_id' => $node->parent_id,
            ];

            if ($node->relationLoaded('childrenRecursive')) {
                $result = array_merge(
                    $result,
                    $this->flattenNodes($node->childrenRecursive, $depth + 1)
                );
            }
        }

        return $result;
    }
}
