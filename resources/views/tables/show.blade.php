@extends('layouts.app', ['title' => $table->name])

@section('content')
    @php
        $primaryColumns = $table->columns->where('section', 'primary')->values();
        $resultColumns = $table->columns->where('section', 'result')->values();
    @endphp
    <x-modal class="table-record-modal" variant="editor" :expandable="true" :title="$table->name" :close-url="route('workspace.tables')">
    <div class="table-workspace">
    <dl class="table-meta"><dt>Строк</dt><dd>{{ $table->row_count }}</dd></dl>
    <nav class="tabs"><a class="{{ $tab === 'table' ? 'active' : '' }}" href="{{ route('workspace.tables.show', $table) }}">Таблица</a><a class="{{ $tab === 'values' ? 'active' : '' }}" href="{{ route('workspace.tables.show', ['referenceTable' => $table, 'tab' => 'values']) }}">Значения</a><a class="{{ $tab === 'script' ? 'active' : '' }}" href="{{ route('workspace.tables.show', ['referenceTable' => $table, 'tab' => 'script']) }}">Скрипт</a></nav>
    @if ($tab === 'values')
        <div class="reference-table-wrap"><table class="reference-table values-editor"><thead>
            @if ($resultColumns->isNotEmpty())
                <tr>@foreach ($primaryColumns as $column)<th rowspan="2">{{ $column->name }}</th>@endforeach<th colspan="{{ $resultColumns->count() }}">Результаты тестирования</th></tr>
                <tr>@foreach ($resultColumns as $column)<th>{{ $column->name }}</th>@endforeach</tr>
            @else
                <tr>@foreach ($primaryColumns as $column)<th>{{ $column->name }}</th>@endforeach</tr>
            @endif
        </thead><tbody>
            @forelse ($table->default_values ?? [] as $rowIndex => $values)
                @php $samples = $table->default_result_values[$rowIndex] ?? [[]]; if ($samples === []) $samples = [[]]; $span = max(count($samples), 1); @endphp
                @foreach ($samples as $sampleIndex => $sampleValues)
                    <tr>
                        @if ($sampleIndex === 0)
                            @foreach ($primaryColumns as $columnIndex => $column)<td rowspan="{{ $span }}">{{ $values[$columnIndex] ?? '' }}</td>@endforeach
                        @endif
                        @foreach ($resultColumns as $columnIndex => $column)<td>{{ $sampleValues[$columnIndex] ?? '' }}</td>@endforeach
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="{{ max(1, $primaryColumns->count() + $resultColumns->count()) }}" class="muted">Значения по умолчанию не заданы.</td></tr>
            @endforelse
        </tbody></table></div>
    @elseif ($tab === 'script')
        <pre class="script-box">{{ $table->script ?: 'Скрипт не задан.' }}</pre>
    @else
        <div class="reference-table-wrap"><table class="reference-table editor-table"><thead><tr><th title="Результат тестирования"><i data-lucide="list-checks"></i></th><th>Название</th><th>Тип</th><th title="Обязательное поле"><i data-lucide="star"></i></th><th title="Поле доступно для редактирования"><i data-lucide="square-pen"></i></th><th>Ширина</th></tr></thead><tbody>@foreach ($table->columns->sortBy(fn ($column) => [$column->section === 'result' ? 1 : 0, $column->position]) as $column)<tr><td class="check-cell"><input type="checkbox" @checked($column->section === 'result') disabled></td><td>{{ $column->name }}</td><td>{{ $column->data_type }}@php($settings = (array) ($column->settings ?? []))@if ($column->elma_type === 'boolean')<small class="muted"> · {{ ($settings['b1_text'] ?? 'Да').' / '.($settings['b0_text'] ?? 'Нет') }}</small>@elseif ($column->elma_type === 'select')<small class="muted"> · {{ implode(', ', $settings['list'] ?? []) }}</small>@endif</td><td class="check-cell"><input type="checkbox" @checked($column->is_required) disabled></td><td class="check-cell"><input type="checkbox" @checked(! $column->is_readonly) disabled></td><td>{{ $column->width }}</td></tr>@endforeach</tbody></table></div>
    @endif
    </div>
    <x-slot:service><x-table-service-information :table="$table" /></x-slot:service>
    <x-slot:actions><a class="button" data-remote-modal href="{{ route('workspace.tables.edit', $table) }}">Редактировать</a><a class="button secondary" href="{{ route('workspace.tables') }}">Закрыть</a></x-slot:actions>
    </x-modal>
@endsection
