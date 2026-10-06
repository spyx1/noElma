<?php

namespace App\Http\Controllers;

use App\Services\KvcService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KvcController extends Controller
{
    public function __construct(private readonly KvcService $kvc)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('kvc.index', [
            'kvcUsers' => $this->kvc->users(),
            'kvcCurrentUser' => [
                'id' => (string) $user->id,
                'fio' => $user->displayName(),
                'isAdmin' => $user->isAdmin(),
            ],
        ]);
    }

    public function tree(): JsonResponse
    {
        return new JsonResponse($this->kvc->getTree());
    }

    public function updateTree(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tree' => ['required', 'array'],
        ]);

        return new JsonResponse($this->kvc->updateTree($request->user(), $data['tree']));
    }

    public function updateValue(Request $request, string $nodeId): JsonResponse
    {
        $data = $request->validate([
            'value' => ['nullable'],
        ]);

        return new JsonResponse($this->kvc->updateNodeValue($request->user(), $nodeId, $data['value'] ?? null));
    }

    public function meetings(Request $request, string $nodeId): JsonResponse
    {
        return new JsonResponse($this->kvc->getMeetings($request->user(), $nodeId));
    }

    public function saveMeeting(Request $request, string $nodeId): JsonResponse
    {
        $data = $request->validate([
            'meeting' => ['required', 'array'],
        ]);

        return new JsonResponse($this->kvc->saveMeeting($request->user(), $nodeId, $data['meeting']));
    }

    public function updateParticipant(Request $request, string $nodeId, string $meetingId): JsonResponse
    {
        $data = $request->validate([
            'userId' => ['nullable', 'string'],
            'userName' => ['nullable', 'string'],
            'sectionNodeId' => ['nullable', 'string'],
            'data' => ['required', 'array'],
        ]);

        return new JsonResponse($this->kvc->updateOwnMeetingParticipant(
            $request->user(),
            $nodeId,
            $meetingId,
            (string) ($data['userId'] ?? ''),
            (string) ($data['userName'] ?? ''),
            $data['data'],
            isset($data['sectionNodeId']) ? (string) $data['sectionNodeId'] : null,
        ));
    }

    public function deleteParticipant(Request $request, string $nodeId, string $meetingId): JsonResponse
    {
        $data = $request->validate([
            'userId' => ['nullable', 'string'],
            'userName' => ['nullable', 'string'],
            'sectionNodeId' => ['nullable', 'string'],
        ]);

        return new JsonResponse($this->kvc->deleteMeetingParticipant(
            $request->user(),
            $nodeId,
            $meetingId,
            (string) ($data['userId'] ?? ''),
            (string) ($data['userName'] ?? ''),
            isset($data['sectionNodeId']) ? (string) $data['sectionNodeId'] : null,
        ));
    }

    public function deleteMeeting(Request $request, string $nodeId, string $meetingId): JsonResponse
    {
        return new JsonResponse([
            'deleted' => $this->kvc->deleteMeeting($request->user(), $nodeId, $meetingId),
        ]);
    }
}
