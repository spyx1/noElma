@extends('layouts.app', ['title' => 'Таблицы'])

@section('content')
    <x-catalog-page title="Таблицы" :create-route="route('workspace.tables.create')" create-label="Создать таблицу">
    <x-flash-status />
    <x-data-table id="reference-tables-v4" :records-count="$tables->count()" search-placeholder="Поиск по полю Название" :columns="['name' => 'Название', 'creator' => 'Автор', 'created' => 'Создан', 'updated' => 'Последнее редактирование', 'editor' => 'Редактор']" :filter-fields="['name' => ['label' => 'Название'], 'creator' => ['label' => 'Автор', 'type' => 'user', 'options' => $filterUsers], 'created' => ['label' => 'Создан', 'type' => 'date'], 'updated' => ['label' => 'Последнее редактирование', 'type' => 'date'], 'editor' => ['label' => 'Редактор', 'type' => 'user', 'options' => $filterUsers]]" :default-visible="['name', 'creator', 'updated']" :bulk-delete-route="route('workspace.tables.bulk-destroy')">
        @forelse ($tables as $table)
            <tr><td class="select-column"><input class="row-select" type="checkbox" value="{{ $table->id }}" aria-label="Выбрать таблицу"></td><td data-column="name"><a class="table-record-link" href="{{ route('workspace.tables.show', $table) }}">{{ $table->name }}</a></td><td data-column="creator" data-filter-value="{{ $table->created_by }}">@if ($table->creator)<a class="user-card-link user-cell" href="{{ route('workspace.users.show', ['user' => $table->creator, 'return_to' => url()->full()]) }}"><x-user-avatar :user="$table->creator" :size="24" />{{ $table->creator->displayName() }}</a>@else Не указан @endif</td><td data-column="created" data-filter-value="{{ $table->created_at->format('Y-m-d') }}">{{ $table->created_at->format('d.m.Y H:i') }}</td><td data-column="updated" data-filter-value="{{ $table->updated_at->format('Y-m-d') }}">{{ $table->updated_at->format('d.m.Y H:i') }}</td><td data-column="editor" data-filter-value="{{ $table->updated_by }}">@if ($table->editor)<a class="user-card-link user-cell" href="{{ route('workspace.users.show', ['user' => $table->editor, 'return_to' => url()->full()]) }}"><x-user-avatar :user="$table->editor" :size="24" />{{ $table->editor->displayName() }}</a>@else Не указан @endif</td></tr>
        @empty
            <tr><td colspan="7" class="muted">Таблиц пока нет.</td></tr>
        @endforelse
    </x-data-table>
    </x-catalog-page>
    @if (isset($modalUser))
        <x-modal class="user-card-modal user-view-card" :title="$modalUser->displayName()" :close-url="$modalReturnTo">
            <dl class="table-meta"><dt>Email</dt><dd>{{ $modalUser->email }}</dd><dt>Телефон</dt><dd>{{ $modalUser->phone ?: '—' }}</dd><dt>Роль</dt><dd><span class="badge">{{ \App\Models\User::roles()[$modalUser->role] }}</span></dd><dt>Создан</dt><dd>{{ $modalUser->created_at->format('d.m.Y H:i') }}</dd></dl>
            <x-slot:actions><a class="button" href="{{ route('workspace.users.edit', ['user' => $modalUser, 'return_to' => $modalReturnTo]) }}">Редактировать</a><a class="button secondary" href="{{ $modalReturnTo }}">Закрыть</a></x-slot:actions>
        </x-modal>
    @endif
@endsection
