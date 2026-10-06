@extends('layouts.app', ['title' => 'Создать пользователя'])

@section('content')
    <x-modal title="Создать пользователя" :close-url="route('workspace.users.index')">
    <form id="user-create-form" method="POST" action="{{ route('workspace.users.store') }}" enctype="multipart/form-data">
        @csrf
        <label>Фамилия <input type="text" name="last_name" value="{{ old('last_name') }}" required></label>
        @error('last_name') <p class="error">{{ $message }}</p> @enderror
        <label>Имя <input type="text" name="first_name" value="{{ old('first_name') }}" required></label>
        @error('first_name') <p class="error">{{ $message }}</p> @enderror
        <label>Отчество <input type="text" name="middle_name" value="{{ old('middle_name') }}"></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        @error('email') <p class="error">{{ $message }}</p> @enderror
        <label>Телефон <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+7 900 000-00-00"></label>
        <label>Аватар
            <select name="avatar_mode"><option value="initials" @selected(old('avatar_mode', 'initials') === 'initials')>Инициалы</option><option value="gravatar" @selected(old('avatar_mode') === 'gravatar')>Gravatar по email</option><option value="upload" @selected(old('avatar_mode') === 'upload')>Загрузить изображение</option></select>
        </label>
        <label>Изображение <input type="file" name="avatar" accept="image/*"></label>
        @error('phone') <p class="error">{{ $message }}</p> @enderror
        <label>Пароль <input type="password" name="password" required minlength="8"></label>
        <label>Повторите пароль <input type="password" name="password_confirmation" required></label>
        @error('password') <p class="error">{{ $message }}</p> @enderror
        <label>Роль
            <select name="role" required>
                @foreach (\App\Models\User::roles() as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', \App\Models\User::ROLE_TESTER) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        @error('role') <p class="error">{{ $message }}</p> @enderror
    </form>
    <x-slot:actions><button type="submit" form="user-create-form">Создать</button><a class="button secondary" href="{{ route('workspace.users.index') }}">Отмена</a></x-slot:actions>
    </x-modal>
@endsection
