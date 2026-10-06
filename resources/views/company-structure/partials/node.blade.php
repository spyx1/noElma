@php
    $children = $node->childrenRecursive ?? collect();
    $hasChildren = $children->isNotEmpty();
    $userIds = $node->users->pluck('id')->values();
@endphp

<li class="structure-node" data-node-id="{{ $node->id }}">
    <div class="structure-node-row" data-search-text="{{ mb_strtolower($node->title.' '.$node->users->map(fn($user) => $user->displayName())->implode(' ')) }}">
        <button
            class="structure-toggle {{ $hasChildren ? '' : 'is-placeholder' }}"
            type="button"
            data-structure-toggle="{{ $node->id }}"
            aria-label="{{ $hasChildren ? 'Свернуть или развернуть' : '' }}"
            @disabled(!$hasChildren)
        >
            <i class="ti ti-chevron-down"></i>
        </button>

        <span class="structure-node-icon">
            @if($node->allows_multiple_users || $node->users->count() > 1)
                <i class="ti ti-users"></i>
            @elseif($node->users->count() === 1)
                <i class="ti ti-user"></i>
            @else
                <i class="ti ti-building"></i>
            @endif
        </span>

        <div class="structure-node-main">
            <div class="structure-node-title">{{ $node->title }}</div>
            @if($node->users->isNotEmpty())
                <div class="structure-node-users">
                    @foreach($node->users as $user)
                        <a class="structure-node-user-link" href="{{ route('workspace.users.show', $user) }}">{{ $user->displayName() }}</a>@if(!$loop->last), @endif
                    @endforeach
                </div>
            @elseif($node->allows_multiple_users)
                <div class="structure-node-users is-empty">Группа пока без сотрудников</div>
            @endif
        </div>

        @if($canEdit)
            <div class="structure-node-actions">
                <button class="structure-icon-button" type="button" title="Добавить дочерний узел" data-add-child="{{ $node->id }}">
                    <i class="ti ti-plus"></i>
                </button>
                <button
                    class="structure-icon-button"
                    type="button"
                    title="Редактировать"
                    data-edit-node="{{ $node->id }}"
                    data-node-title="{{ $node->title }}"
                    data-parent-id="{{ $node->parent_id }}"
                    data-allows-multiple="{{ $node->allows_multiple_users ? '1' : '0' }}"
                    data-user-ids='@json($userIds)'
                >
                    <i class="ti ti-pencil"></i>
                </button>
                <button class="structure-icon-button danger-outline" type="button" title="Удалить" data-delete-node="{{ $node->id }}" data-node-title="{{ $node->title }}">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        @endif
    </div>

    @if($hasChildren)
        <ul class="structure-children" data-children-for="{{ $node->id }}">
            @foreach($children as $child)
                @include('company-structure.partials.node', ['node' => $child, 'canEdit' => $canEdit])
            @endforeach
        </ul>
    @endif
</li>
