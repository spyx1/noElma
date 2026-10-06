@extends('layouts.app', ['title' => $additionalWorkType->name])

@section('content')
@php
    $executionTime = is_null($additionalWorkType->execution_time)
        ? '—'
        : rtrim(rtrim(number_format((float) $additionalWorkType->execution_time, 2, '.', ''), '0'), '.');
@endphp
<x-modal class="table-record-modal" :title="$additionalWorkType->name" :close-url="route('workspace.additional-work-types')">

    <dl class="table-meta">
        <dt>Время выполнения</dt><dd>{{ $executionTime }}</dd>
        <dt>Показывать количество</dt><dd>{{ $additionalWorkType->show_quantity ? 'Да' : 'Нет' }}</dd>
        <dt>Описание обязательно</dt><dd>{{ $additionalWorkType->description_required ? 'Да' : 'Нет' }}</dd>
        <dt>Создан</dt><dd>{{ $additionalWorkType->created_at->format('d.m.Y H:i') }}</dd>
        <dt>Последнее редактирование</dt><dd>{{ $additionalWorkType->updated_at->format('d.m.Y H:i') }}</dd>
    </dl>

    <x-slot:actions><a class="button" href="{{ route('workspace.additional-work-types.edit', $additionalWorkType) }}">Редактировать</a><a class="button secondary" href="{{ route('workspace.additional-work-types') }}">Закрыть</a></x-slot:actions>
</x-modal>
@endsection
