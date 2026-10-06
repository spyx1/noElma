@extends('layouts.app', ['title' => 'Пользователи'])

@section('content')
    <x-catalog-page title="Пользователи" :create-route="route('workspace.users.create')" create-label="Создать пользователя">
    <x-flash-status />
    <x-data-table id="users" :records-count="$users->count()" search-placeholder="Поиск по полю ФИО" :columns="['full-name' => 'ФИО', 'phone' => 'Телефон', 'email' => 'Email', 'role' => 'Роль', 'created' => 'Создан']" :filter-fields="['full-name' => ['label' => 'ФИО'], 'phone' => ['label' => 'Телефон'], 'email' => ['label' => 'Email'], 'role' => ['label' => 'Роль', 'type' => 'select', 'options' => \App\Models\User::roles()], 'created' => ['label' => 'Создан', 'type' => 'date']]" :default-visible="['full-name', 'email', 'role', 'created']" :bulk-delete-route="route('workspace.users.bulk-destroy')">
        @forelse ($users as $user)
            <tr><td class="select-column"><input class="row-select" type="checkbox" value="{{ $user->id }}" aria-label="Выбрать пользователя"></td><td data-column="full-name"><a class="user-cell" href="{{ route('workspace.users.show', $user) }}"><x-user-avatar :user="$user" :size="26" />{{ $user->displayName() }}</a></td><td data-column="phone">{{ $user->phone ?: '—' }}</td><td data-column="email">{{ $user->email ?: '—' }}</td><td data-column="role" data-filter-value="{{ $user->role }}"><span class="badge">{{ \App\Models\User::roles()[$user->role] }}</span></td><td data-column="created" data-filter-value="{{ $user->created_at->format('Y-m-d') }}">{{ $user->created_at->format('d.m.Y H:i') }}</td></tr>
        @empty
            <tr><td colspan="6">Пользователей нет.</td></tr>
        @endforelse
    </x-data-table>
    </x-catalog-page>
@endsection
