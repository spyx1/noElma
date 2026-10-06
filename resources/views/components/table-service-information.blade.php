@props(['table'])

<dl class="table-meta">
    <dt>ELMA UUID</dt>
    <dd>{{ $table->external_id ?? '—' }}</dd>
    <dt>Автор</dt>
    <dd>
        @if ($table->creator)
            <a class="user-card-link user-cell" href="{{ route('workspace.users.show', ['user' => $table->creator, 'return_to' => url()->full()]) }}"><x-user-avatar :user="$table->creator" :size="24" />{{ $table->creator->displayName() }}</a>
        @else
            Не указан
        @endif
        <span class="service-date">· {{ $table->created_at->format('d.m.Y H:i') }}</span>
    </dd>
    <dt>Последнее изменение</dt>
    <dd>
        @if ($table->editor)
            <a class="user-card-link user-cell" href="{{ route('workspace.users.show', ['user' => $table->editor, 'return_to' => url()->full()]) }}"><x-user-avatar :user="$table->editor" :size="24" />{{ $table->editor->displayName() }}</a>
        @else
            Не указан
        @endif
        <span class="service-date">· {{ $table->updated_at->format('d.m.Y H:i') }}</span>
    </dd>
</dl>
