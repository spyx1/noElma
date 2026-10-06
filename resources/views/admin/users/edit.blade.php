@extends('layouts.app', ['title' => 'Редактировать пользователя'])

@section('content')
    <x-modal title="Редактировать пользователя" :close-url="route('workspace.users.index')">
        <form id="user-edit-form" method="POST" action="{{ route('workspace.users.update', $user) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label>Фамилия <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required></label>
            @error('last_name') <p class="error">{{ $message }}</p> @enderror
            <label>Имя <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required></label>
            @error('first_name') <p class="error">{{ $message }}</p> @enderror
            <label>Отчество <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"></label>
            <label>Email <input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
            @error('email') <p class="error">{{ $message }}</p> @enderror
            <label>Телефон <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+7 900 000-00-00"></label>
            <label>Аватар
                <select name="avatar_mode"><option value="initials" @selected(old('avatar_mode', $user->avatar_mode) === 'initials')>Инициалы</option><option value="gravatar" @selected(old('avatar_mode', $user->avatar_mode) === 'gravatar')>Gravatar по email</option><option value="upload" @selected(old('avatar_mode', $user->avatar_mode) === 'upload')>Загрузить изображение</option></select>
            </label>
            <label>Новое изображение <input type="file" name="avatar" accept="image/*"></label>
            @error('phone') <p class="error">{{ $message }}</p> @enderror
            <label>Роль
                <select name="role" required>
                    @foreach (\App\Models\User::roles() as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <div id="password-fields" hidden>
                <label>Новый пароль <input type="password" id="password" minlength="8" autocomplete="new-password"></label>
                <label>Повторите новый пароль <input type="password" id="password-confirmation" autocomplete="new-password"></label>
            </div>
            @error('password') <p class="error">{{ $message }}</p> @enderror
            @error('password_confirmation') <p class="error">{{ $message }}</p> @enderror
        </form>
        <x-slot:actions><button type="submit" form="user-edit-form">Сохранить</button><a class="button secondary" href="{{ route('workspace.users.index') }}">Отмена</a><button class="password-action" type="button" id="password-toggle">Изменить пароль</button><button class="danger" type="button" id="delete-toggle">Удалить</button></x-slot:actions>
    </x-modal>
    <x-modal id="delete-modal" variant="confirm" title="Удалить пользователя?" :hidden="true" close-button-id="delete-cancel"><p class="muted">Это действие нельзя отменить.</p><form id="delete-user-form" method="POST" action="{{ route('workspace.users.destroy', $user) }}">@csrf @method('DELETE')</form><x-slot:actions><button class="danger" type="submit" form="delete-user-form">Удалить</button><button class="secondary" type="button" id="delete-cancel-footer">Отмена</button></x-slot:actions></x-modal>
    <script>
        document.getElementById('password-toggle').addEventListener('click', function () {
            document.getElementById('password-fields').hidden = false;
            document.getElementById('password').name = 'password';
            document.getElementById('password-confirmation').name = 'password_confirmation';
            this.hidden = true;
        });
        document.getElementById('delete-toggle').addEventListener('click', () => document.getElementById('delete-modal').hidden = false);
        document.querySelectorAll('#delete-cancel, #delete-cancel-footer').forEach((button) => button.addEventListener('click', () => document.getElementById('delete-modal').hidden = true));
    </script>
@endsection
