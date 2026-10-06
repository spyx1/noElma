@extends('layouts.app', ['title' => $user->displayName()])

@section('content')
<x-modal class="user-view-card" :title="$user->displayName()" :close-url="$returnTo">
    <dl class="table-meta"><dt>Email</dt><dd>{{ $user->email ?: '—' }}</dd><dt>Телефон</dt><dd>{{ $user->phone ?: '—' }}</dd><dt>Роль</dt><dd><span class="badge">{{ \App\Models\User::roles()[$user->role] }}</span></dd><dt>Создан</dt><dd>{{ $user->created_at->format('d.m.Y H:i') }}</dd></dl>
    <x-slot:actions><a class="button" href="{{ route('workspace.users.edit', ['user' => $user, 'return_to' => $returnTo]) }}">Редактировать</a><a class="button secondary" href="{{ $returnTo }}">Закрыть</a></x-slot:actions>
</x-modal>
@endsection
