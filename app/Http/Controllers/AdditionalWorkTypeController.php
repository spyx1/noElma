<?php

namespace App\Http\Controllers;

use App\Models\AdditionalWorkType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdditionalWorkTypeController extends Controller
{
    public function index(): View
    {
        return view('additional-work-types.index', [
            'additionalWorkTypes' => AdditionalWorkType::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('additional-work-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $additionalWorkType = AdditionalWorkType::create($this->validatedData($request));

        return redirect()
            ->route('workspace.additional-work-types.show', $additionalWorkType)
            ->with('status', 'Вид доп. работ создан.');
    }

    public function show(AdditionalWorkType $additionalWorkType): View
    {
        return view('additional-work-types.show', compact('additionalWorkType'));
    }

    public function edit(AdditionalWorkType $additionalWorkType): View
    {
        return view('additional-work-types.edit', compact('additionalWorkType'));
    }

    public function update(Request $request, AdditionalWorkType $additionalWorkType): RedirectResponse
    {
        $additionalWorkType->update($this->validatedData($request));

        return redirect()
            ->route('workspace.additional-work-types.show', $additionalWorkType)
            ->with('status', 'Вид доп. работ обновлён.');
    }

    public function destroy(AdditionalWorkType $additionalWorkType): RedirectResponse
    {
        $additionalWorkType->delete();

        return redirect()
            ->route('workspace.additional-work-types')
            ->with('status', 'Вид доп. работ удалён.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:additional_work_types,id'],
        ])['ids'];

        $deleted = AdditionalWorkType::query()->whereIn('id', $ids)->delete();

        return redirect()
            ->route('workspace.additional-work-types')
            ->with('status', "Удалено видов доп. работ: {$deleted}.");
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'execution_time' => ['nullable', 'numeric', 'min:0'],
            'show_quantity' => ['nullable', 'boolean'],
            'description_required' => ['nullable', 'boolean'],
        ]);

        $validated['show_quantity'] = $request->boolean('show_quantity');
        $validated['description_required'] = $request->boolean('description_required');

        return $validated;
    }
}
