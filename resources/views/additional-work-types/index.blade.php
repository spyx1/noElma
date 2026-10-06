@extends('layouts.app', ['title' => 'Виды доп. работ'])

@section('content')
    <x-catalog-page
        title="Виды доп. работ"
        :create-route="route('workspace.additional-work-types.create')"
        create-label="Создать вид доп. работ"
    >
        <x-flash-status />

        <x-data-table
            id="additional-work-types"
            :records-count="$additionalWorkTypes->count()"
            search-placeholder="Поиск по названию"
            empty-message="Видов доп. работ пока нет."
            :columns="[
                'name' => 'Название',
                'execution_time' => 'Время выполнения',
                'show_quantity' => 'Показывать количество',
                'description_required' => 'Описание обязательно',
                'updated' => 'Последнее редактирование',
            ]"
            :filter-fields="[
                'name' => ['label' => 'Название'],
                'execution_time' => ['label' => 'Время выполнения'],
                'show_quantity' => ['label' => 'Показывать количество', 'type' => 'select', 'options' => ['1' => 'Да', '0' => 'Нет']],
                'description_required' => ['label' => 'Описание обязательно', 'type' => 'select', 'options' => ['1' => 'Да', '0' => 'Нет']],
                'updated' => ['label' => 'Последнее редактирование', 'type' => 'date'],
            ]"
            :default-visible="['name', 'execution_time', 'show_quantity', 'description_required', 'updated']"
            :bulk-delete-route="route('workspace.additional-work-types.bulk-destroy')"
            catalog-kind="standard"
        >
            @forelse ($additionalWorkTypes as $additionalWorkType)
                @php
                    $executionTime = is_null($additionalWorkType->execution_time)
                        ? '—'
                        : rtrim(rtrim(number_format((float) $additionalWorkType->execution_time, 2, '.', ''), '0'), '.');
                @endphp

                <tr>
                    <td class="select-column">
                        <input
                            class="row-select"
                            type="checkbox"
                            value="{{ $additionalWorkType->id }}"
                            aria-label="Выбрать {{ $additionalWorkType->name }}"
                        >
                    </td>
                    <td data-column="name">
                        <a
                            class="table-record-link"
                            href="{{ route('workspace.additional-work-types.show', $additionalWorkType) }}"
                        >
                            {{ $additionalWorkType->name }}
                        </a>
                    </td>
                    <td data-column="execution_time">{{ $executionTime }}</td>
                    <td data-column="show_quantity">{{ $additionalWorkType->show_quantity ? 'Да' : 'Нет' }}</td>
                    <td data-column="description_required">{{ $additionalWorkType->description_required ? 'Да' : 'Нет' }}</td>
                    <td
                        data-column="updated"
                        data-filter-value="{{ $additionalWorkType->updated_at->format('Y-m-d') }}"
                    >
                        {{ $additionalWorkType->updated_at->format('d.m.Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Видов доп. работ пока нет.</td></tr>
            @endforelse
        </x-data-table>
    </x-catalog-page>
@endsection
