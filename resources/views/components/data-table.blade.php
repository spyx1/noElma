@props([
    'id', 'columns', 'filterFields' => [], 'recordsCount' => null,
    'selectionUrl' => null, 'searchPlaceholder' => 'Поиск',
    'bulkDeleteRoute' => null, 'defaultVisible' => [],
    'emptyMessage' => 'Нет данных.', 'catalogKind' => 'reference',
])

@php
    // Все настройки клиентской таблицы лежат здесь, чтобы новый справочник
    // подключал общий компонент без копирования JavaScript.
    $tableConfig = [
        'id' => $id, 'kind' => $catalogKind, 'columns' => $columns,
        'defaultVisible' => $defaultVisible, 'emptyMessage' => $emptyMessage,
        'recordsCount' => $recordsCount,
        'selectionUrl' => $selectionUrl ?? request()->url().'/selection-ids',
        'hasBulkDelete' => filled($bulkDeleteRoute),
    ];
@endphp

<div class="data-table is-initializing" data-catalog-table data-catalog-config='@json($tableConfig)'>
    <div class="data-table-tools">
        <div class="table-search">
            <i data-lucide="search"></i>
            <input type="search" placeholder="{{ $searchPlaceholder }}" aria-label="Поиск по таблице">
            @if (! is_null($recordsCount)) <span class="data-table-count">Элементов: {{ $recordsCount }}</span> @endif
            <button class="table-filter-button" type="button" aria-label="Фильтр" title="Фильтр"><i data-lucide="funnel"></i></button>
        </div>
        <div class="table-filter-status" hidden>
            <i data-lucide="funnel"></i><span></span>
            <button type="button" class="clear-active-filter" aria-label="Сбросить фильтр" title="Сбросить фильтр">×</button>
        </div>
        @if ($bulkDeleteRoute)
            <button class="table-bulk-delete" type="button" hidden><i data-lucide="trash-2"></i><span>Удалить выбранные</span><span class="bulk-delete-count"></span></button>
        @endif
        <div class="table-menu">
            <button class="table-menu-button" type="button" aria-label="Действия с таблицей" title="Действия с таблицей"><i data-lucide="settings-2"></i></button>
            <div class="table-actions-menu" hidden>
                <button class="export-excel" type="button"><i data-lucide="download"></i> Выгрузить в Excel</button>
                <button class="table-settings" type="button"><i data-lucide="columns-3"></i> Настройка столбцов</button>
            </div>
        </div>
    </div>

    <div class="table-settings-panel" hidden>
        <div class="table-settings-header">
            <div><strong>Столбцы</strong><span>Перетаскивайте, чтобы изменить порядок</span></div>
            <button class="close-table-settings" type="button" aria-label="Закрыть" title="Закрыть"><i data-lucide="x"></i></button>
        </div>
        <ul class="column-settings-list"></ul>
        <div class="table-settings-footer"><button class="reset-columns" type="button">Сбросить</button></div>
    </div>

    <x-modal class="table-filter-overlay" dialog-class="table-filter-panel" title="Фильтр" :hidden="true" close-button-class="close-filter">
            <div class="filter-fields">
                @foreach ($filterFields ?: $columns as $key => $field)
                    @php($definition = is_array($field) ? $field : ['label' => $field])
                    @continue(blank($definition['label'] ?? null))
                    <label>
                        {{ $definition['label'] }}
                        @if (in_array($definition['type'] ?? 'text', ['select', 'user'], true))
                            <select class="filter-value {{ ($definition['type'] ?? '') === 'user' ? 'app-user-select' : '' }}" data-filter-key="{{ $key }}"><option value="">Любое значение</option>@foreach ($definition['options'] ?? [] as $value => $option)<option value="{{ $value }}">{{ $option }}</option>@endforeach</select>
                        @else
                            <input class="filter-value" data-filter-key="{{ $key }}" type="{{ $definition['type'] ?? 'search' }}" placeholder="{{ ($definition['type'] ?? '') === 'date' ? '' : 'Любое значение' }}">
                        @endif
                    </label>
                @endforeach
            </div>
            <x-slot:actions><div class="filter-actions"><button class="apply-filter" type="button"><i data-lucide="check"></i> Применить</button><button class="reset-filter" type="button"><i data-lucide="rotate-ccw"></i> Сбросить</button><button class="cancel-filter" type="button"><i data-lucide="x"></i> Отмена</button></div></x-slot:actions>
    </x-modal>

    <div class="data-table-loader" role="status" aria-label="Загрузка таблицы">
        <span class="data-table-spinner"></span>
        <span class="data-table-loader-copy"><strong>Загружаем справочник</strong><small>Подготавливаем таблицу…</small></span>
    </div>
    <div class="reference-table-wrap data-table-wrap">
        <table id="data-grid-{{ $id }}" class="reference-table" data-table-grid>
            <thead><tr>
                @if ($bulkDeleteRoute)<th class="select-column"><input type="checkbox" class="select-all" aria-label="Выбрать все"></th>@endif
                @foreach ($columns as $key => $label)<th data-column="{{ $key }}">{{ $label }}<span class="column-resizer" aria-hidden="true" title="Потяните, чтобы изменить ширину"></span></th>@endforeach
            </tr></thead>
            <tbody>{{ $slot }}</tbody>
        </table>
    </div>

    <button class="table-back-to-top" type="button" hidden aria-label="Вернуться наверх" title="Наверх"><i data-lucide="arrow-up"></i></button>
    @if ($bulkDeleteRoute)<form method="POST" action="{{ $bulkDeleteRoute }}" class="bulk-delete-form" hidden>@csrf @method('DELETE')</form>@endif
</div>
