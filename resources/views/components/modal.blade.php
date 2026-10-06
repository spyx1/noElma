@props([
    'title' => null,
    'closeUrl' => null,
    'variant' => 'default',
    'hidden' => false,
    'closeButtonId' => null,
    'closeButtonClass' => null,
    'dialogClass' => null,
    'expandable' => false,
])

<div {{ $attributes->class(['modal', 'app-modal', "app-modal--{$variant}"]) }} {{ $hidden ? 'hidden' : '' }}>
    <section @class(['card', 'app-modal__dialog', $dialogClass]) role="dialog" aria-modal="true" aria-label="{{ $title ?: 'Диалоговое окно' }}">
        <header class="app-modal__header">
            @isset($header)
                {{ $header }}
            @else
                <h2>{{ $title }}</h2>
                <div class="app-modal__header-actions">
                    @isset($service)
                        <button class="app-modal__utility" type="button" data-modal-service-toggle aria-label="Служебная информация" aria-expanded="false" title="Служебная информация"><i data-lucide="info"></i></button>
                    @endisset
                    @if($expandable)
                        <button class="app-modal__utility" type="button" data-modal-expand aria-label="Развернуть на весь экран" aria-pressed="false" title="Развернуть на весь экран"><i data-lucide="maximize-2"></i></button>
                    @endif
                    @if($closeUrl)
                        <a class="app-modal__close" href="{{ $closeUrl }}" aria-label="Закрыть" title="Закрыть"><i data-lucide="x"></i></a>
                    @elseif($closeButtonId || $closeButtonClass)
                        <button class="app-modal__close {{ $closeButtonClass }}" type="button" id="{{ $closeButtonId }}" aria-label="Закрыть" title="Закрыть"><i data-lucide="x"></i></button>
                    @endif
                </div>
            @endisset
        </header>

        @isset($service)
            <section class="app-modal__service" data-modal-service hidden>
                {{ $service }}
            </section>
        @endisset

        <div class="app-modal__body">
            {{ $slot }}
        </div>

        @isset($actions)
            <footer class="app-modal__footer">
                {{ $actions }}
            </footer>
        @endisset
    </section>
</div>
