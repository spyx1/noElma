@extends('layouts.app', ['title' => 'Редактировать вид доп. работ'])

@section('content')
<x-modal variant="editor" title="Редактировать вид доп. работ" :close-url="route('workspace.additional-work-types.show', $additionalWorkType)">
    <form id="additional-work-type-form" method="POST" action="{{ route('workspace.additional-work-types.update', $additionalWorkType) }}">
        @csrf
        @method('PUT')
        <div class="editor-details additional-work-type-editor-details">
            <label>
                <span>Название <em>*</em></span>
                <input type="text" name="name" value="{{ old('name', $additionalWorkType->name) }}" required autofocus>
            </label>
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label>
                <span>Время выполнения</span>
                <span>
                    <input type="number" name="execution_time" value="{{ old('execution_time', $additionalWorkType->execution_time) }}" min="0" step="0.01">
                    <small class="muted form-hint">норма</small>
                </span>
            </label>
            @error('execution_time') <p class="error">{{ $message }}</p> @enderror

            <label class="additional-work-type-checkbox-row">
                <span>Показывать количество</span>
                <input type="checkbox" name="show_quantity" value="1" @checked(old('show_quantity', $additionalWorkType->show_quantity))>
            </label>

            <label class="additional-work-type-checkbox-row">
                <span>Описание обязательно</span>
                <input type="checkbox" name="description_required" value="1" @checked(old('description_required', $additionalWorkType->description_required))>
            </label>
        </div>

    </form>
    <x-slot:actions><button type="submit" form="additional-work-type-form">Сохранить</button><a class="button secondary" href="{{ route('workspace.additional-work-types.show', $additionalWorkType) }}">Отмена</a><button class="danger" type="button" id="delete-toggle">Удалить</button></x-slot:actions>
</x-modal>
<x-modal id="delete-modal" variant="confirm" title="Удалить вид доп. работ?" :hidden="true" close-button-id="delete-cancel"><p class="muted">Это действие нельзя отменить.</p><form id="delete-additional-work-type-form" method="POST" action="{{ route('workspace.additional-work-types.destroy', $additionalWorkType) }}">@csrf @method('DELETE')</form><x-slot:actions><button class="danger" type="submit" form="delete-additional-work-type-form">Удалить</button><button class="secondary" type="button" id="delete-cancel-footer">Отмена</button></x-slot:actions></x-modal>

<script>
    document.getElementById('delete-toggle').addEventListener('click', () => document.getElementById('delete-modal').hidden = false);
    document.querySelectorAll('#delete-cancel, #delete-cancel-footer').forEach((button) => button.addEventListener('click', () => document.getElementById('delete-modal').hidden = true));
</script>
@endsection
