@props(['user', 'size' => 30])
@php($letters = mb_strtoupper(mb_substr($user->last_name ?: $user->name, 0, 1) . mb_substr($user->first_name ?: '', 0, 1)))
@php($colors = ['#2f80ed', '#7b61ff', '#d0608c', '#1c9c84', '#d58b23', '#5c7c9e'])
@php($color = $colors[abs(crc32((string) $user->id . $user->email)) % count($colors)])
@if ($user->avatar_path)
    <img class="user-avatar" src="{{ asset('storage/'.$user->avatar_path) }}" width="{{ $size }}" height="{{ $size }}" alt="{{ $user->displayName() }}">
@elseif ($user->avatar_mode === 'gravatar')
    <img class="user-avatar" src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($user->email))) }}?d=identicon&s={{ $size * 2 }}" width="{{ $size }}" height="{{ $size }}" alt="{{ $user->displayName() }}">
@else
    <span class="user-avatar user-avatar-initials" style="--avatar-color:{{ $color }};width:{{ $size }}px;height:{{ $size }}px">{{ $letters ?: 'П' }}</span>
@endif
