@extends('layouts.app', ['title' => 'Добавить вид доп. работ'])

@section('content')
<x-modal variant="editor" title="Добавить вид доп. работ" :close-url="route('workspace.additional-work-types')">
    <form id="additional-work-type-form" method="POST" action="{{ route('workspace.additional-work-types.store') }}">
        @csrf
        <div class="editor-details additional-work-type-editor-details">
            <label>
                <span>Название <em>*</em></span>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus>
            </label>
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label>
                <span>Время выполнения</span>
                <span>
                    <input type="number" name="execution_time" value="{{ old('execution_time') }}" min="0" step="0.01">
                    <small class="muted form-hint">норма</small>
                </span>
            </label>
            @error('execution_time') <p class="error">{{ $message }}</p> @enderror

            <label class="additional-work-type-checkbox-row">
                <span>Показывать количество</span>
                <input type="checkbox" name="show_quantity" value="1" @checked(old('show_quantity'))>
            </label>

            <label class="additional-work-type-checkbox-row">
                <span>Описание обязательно</span>
                <input type="checkbox" name="description_required" value="1" @checked(old('description_required'))>
            </label>
        </div>

    </form>
    <x-slot:actions><button type="submit" form="additional-work-type-form">Создать</button><a class="button secondary" href="{{ route('workspace.additional-work-types') }}">Отмена</a></x-slot:actions>
</x-modal>
@endsection
