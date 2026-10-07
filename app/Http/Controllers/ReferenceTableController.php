<?php

namespace App\Http\Controllers;

use App\Models\ReferenceTable;
use App\Models\ReferenceTableColumn;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ReferenceTableController extends Controller
{
    public function index(): View
    {
        return view('tables.index', $this->indexData());
    }

    public function indexData(): array
    {
        return [
            'tables' => ReferenceTable::query()->with(['creator', 'editor'])->orderByDesc('id')->get(),
            'filterUsers' => User::query()->orderBy('last_name')->get()->mapWithKeys(fn (User $user) => [$user->id => $user->displayName()])->all(),
        ];
    }

    public function show(Request $request, ReferenceTable $referenceTable): View
    {
        return view('tables.show', [
            'table' => $referenceTable->load(['columns', 'creator', 'editor']),
            'tab' => $request->string('tab', 'table')->value(),
        ]);
    }

    public function create(): View
    {
        $table = new ReferenceTable([
            'row_count' => 1,
            'default_values' => [['']],
            'default_result_values' => [],
        ]);
        $table->id = 0;
        $table->created_at = now();
        $table->updated_at = now();
        $table->setRelation('columns', collect([new ReferenceTableColumn([
            'section' => 'primary', 'position' => 1, 'name' => '', 'data_type' => 'Строка',
            'elma_type' => 'string', 'is_required' => false, 'is_readonly' => false,
            'width' => 175, 'settings' => [],
        ])]));

        return view('tables.edit', ['table' => $table, 'creating' => true]);
    }

    public function selectionIds(Request $request): JsonResponse
    {
        $query = ReferenceTable::query();
        if ($search = $request->string('search')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }
        return response()->json(['ids' => $query->pluck('id')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedTable($request);
        $table = DB::transaction(function () use ($data, $request): ReferenceTable {
            $table = ReferenceTable::create([...collect($data)->only(['name', 'row_count', 'script', 'default_values', 'default_result_values'])->all(), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
            $this->syncColumns($table, $data['columns'] ?? []);
            return $table;
        });

        return redirect()->route('workspace.tables.show', $table)->with('status', 'Таблица создана.');
    }

    public function edit(Request $request, ReferenceTable $referenceTable): View
    {
        $tab = $request->string('tab', 'structure')->value();
        $tab = $tab === 'table' ? 'structure' : $tab;

        return view('tables.edit', [
            'table' => $referenceTable->load(['columns', 'creator', 'editor']),
            'tab' => in_array($tab, ['structure', 'values', 'script'], true) ? $tab : 'structure',
        ]);
    }

    public function update(Request $request, ReferenceTable $referenceTable): RedirectResponse
    {
        $data = $this->validatedTable($request);

        DB::transaction(function () use ($referenceTable, $data, $request): void {
            $savedSettings = $referenceTable->columns()->get()->mapWithKeys(fn ($column) => ["{$column->section}:{$column->position}" => (array) $column->settings])->all();
            $referenceTable->update([...collect($data)->only(['name', 'row_count', 'script', 'default_values', 'default_result_values'])->all(), 'updated_by' => $request->user()->id]);
            $referenceTable->columns()->delete();
            $this->syncColumns($referenceTable, $data['columns'] ?? [], $savedSettings);
        });

        return redirect()->route('workspace.tables.show', $referenceTable)->with('status', 'Таблица обновлена.');
    }

    private function validatedTable(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:5000'],
            'row_count' => ['required', 'integer', 'min:0'],
            'script' => ['nullable', 'string'],
            'columns' => ['nullable', 'array'],
            'columns.*.name' => ['nullable', 'string', 'max:255'],
            'columns.*.data_type' => ['required_with:columns', 'in:Строка,Число целое,Число дробное,Дата,Логическое,Список,Файлы,Приложение,Итератор'],
            'columns.*.is_required' => ['nullable', 'boolean'],
            'columns.*.is_key' => ['nullable', 'boolean'],
            'columns.*.width' => ['required_with:columns', 'integer', 'min:40', 'max:1000'],
            'columns.*.values' => ['nullable', 'array'],
            'columns.*.values.*' => ['nullable', 'string'],
            'columns.*.settings' => ['nullable', 'array'],
            'columns.*.settings.b1_text' => ['nullable', 'string', 'max:255'],
            'columns.*.settings.b0_text' => ['nullable', 'string', 'max:255'],
            'columns.*.settings.default_state' => ['nullable', 'boolean'],
            'columns.*.settings.list' => ['nullable'],
            'default_values' => ['nullable', 'array'],
            'default_values.*' => ['array'],
            'default_values.*.*' => ['nullable', 'string'],
            'default_result_values' => ['nullable', 'array'],
            'default_result_values.*' => ['array'],
            'default_result_values.*.*' => ['array'],
            'default_result_values.*.*.*' => ['nullable', 'string'],
        ]);
    }

    private function syncColumns(ReferenceTable $table, array $columns, array $savedSettings = []): void
    {
        $positions = [];
        foreach ($columns as $column) {
            if (blank($column['name'] ?? null)) continue;
            $section = $column['section'] ?? 'primary';
            $position = ($positions[$section] ?? 0) + 1;
            $positions[$section] = $position;
            $table->columns()->create([
                'section' => $section,
                'name' => $column['name'], 'data_type' => $column['data_type'], 'elma_type' => $this->elmaType($column['data_type']),
                'is_required' => (bool) ($column['is_required'] ?? false), 'is_key' => (bool) ($column['is_key'] ?? false),
                'is_readonly' => (bool) ($column['is_readonly'] ?? false),
                'width' => $column['width'], 'values' => array_values($column['values'] ?? []), 'settings' => $this->columnSettings($column['settings'] ?? ($savedSettings["{$section}:{$position}"] ?? []), $column['data_type']), 'position' => $position,
            ]);
        }
    }

    private function columnSettings(array $settings, string $type): array
    {
        if ($type === 'Логическое') return ['b1_text' => $settings['b1_text'] ?? 'Да', 'b0_text' => $settings['b0_text'] ?? 'Нет', 'default_state' => (int) ($settings['default_state'] ?? 1)];
        if ($type === 'Список') {
            $list = $settings['list'] ?? [];
            if (! is_array($list)) $list = preg_split('/\R/u', $list) ?: [];
            return ['list' => array_values(array_filter($list, fn ($item) => $item !== ''))];
        }
        return $settings;
    }

    private function elmaType(string $type): string
    {
        return match ($type) {
            'Число целое' => 'integer', 'Число дробное' => 'float', 'Логическое' => 'boolean',
            'Список' => 'select', 'Файлы' => 'files', 'Приложение' => 'app', 'Итератор' => 'inc', default => 'string',
        };
    }

    public function destroy(ReferenceTable $referenceTable): RedirectResponse
    {
        $referenceTable->delete();

        return redirect()->route('workspace.tables')->with('status', 'Таблица удалена.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer', 'exists:reference_tables,id']])['ids'];
        $deleted = ReferenceTable::query()->whereIn('id', $ids)->delete();

        return redirect()->route('workspace.tables')->with('status', "Удалено таблиц: {$deleted}.");
    }
}
