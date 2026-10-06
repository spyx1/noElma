@props(['title', 'createRoute' => null, 'createLabel' => null])

<section class="catalog-page">
    <div class="catalog-heading">
        <h1>{{ $title }}</h1>
        @if ($createRoute && $createLabel)
            <a class="button btn btn-primary create-user-button create-icon-button" data-remote-modal href="{{ $createRoute }}" title="{{ $createLabel }}"><i class="ti ti-plus"></i><span class="d-none d-sm-inline">{{ $createLabel }}</span></a>
        @endif
    </div>
    {{ $slot }}
</section>
