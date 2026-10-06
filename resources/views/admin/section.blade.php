@extends('layouts.app', ['title' => $title])

@section('content')
    <div class="card">
        <div class="card-body text-center py-5">
            <span class="avatar avatar-xl bg-azure-lt text-azure"><i class="ti ti-layout-list"></i></span>
            <h1 class="mt-3">{{ $title }}</h1>
            <p class="text-secondary mb-0">Раздел готов для наполнения.</p>
        </div>
    </div>
@endsection
