@extends('layouts.app', ['title' => 'Структура компании'])

@push('styles')
<style>
    .structure-tree-card { min-height: 520px; margin: 18px; padding: 16px 18px 28px; border: 1px solid #dce6e3; border-radius: 10px; background: #fff; box-shadow: 0 1px 2px rgba(24,36,51,.04); }
    .structure-tree, .structure-tree ul { margin: 0; padding: 0; list-style: none; }
    .structure-tree { max-width: 1100px; visibility: hidden; }
    .structure-tree.is-ready { visibility: visible; }
    .structure-node { position: relative; }
    .structure-node-row { position: relative; display: flex; align-items: center; min-height: 47px; padding: 4px 6px 4px 0; border-radius: 7px; }
    .structure-node-row:hover { background: #f7fbfa; }
    .structure-toggle { display: grid; flex: 0 0 24px; place-items: center; width: 24px; height: 24px; min-height: 24px; margin-right: 4px; padding: 0 !important; border: 0 !important; border-radius: 5px; background: transparent !important; color: #526778 !important; }
    .structure-toggle:hover { background: #e9f4f1 !important; color: #0f766e !important; }
    .structure-toggle.is-placeholder {
        opacity: .28;
        cursor: default;
        pointer-events: none;
    }
    .structure-toggle.is-placeholder:hover {
        background: transparent !important;
        color: #526778 !important;
    }
    .structure-toggle i { font-size: 16px; transition: transform .15s ease; }
    .structure-node.is-collapsed > .structure-node-row .structure-toggle i { transform: rotate(-90deg); }
    .structure-node.is-collapsed > .structure-children { display: none; }
    .structure-node-icon { display: grid; flex: 0 0 28px; place-items: center; width: 28px; height: 28px; margin-right: 8px; color: #9aa5ad; font-size: 22px; }
    .structure-node-main { min-width: 0; padding: 3px 0; }
    .structure-node-title { color: #0f66a8; font-size: 14px; font-weight: 500; line-height: 1.3; }
    .structure-node-users { margin-top: 2px; color: #7a8790; font-size: 11px; line-height: 1.35; }
    .structure-node-user-link { color: #6f7f89; text-decoration: none; font-weight: 400; }
    .structure-node-user-link:hover { color: #0f66a8; text-decoration: underline; }
    .structure-node-users.is-empty { font-style: italic; color: #9aa5ad; }

    .structure-user-modal { z-index: 1200; }
    .structure-user-modal .structure-user-modal-loading { width: min(100%, 620px); padding: 28px; border-radius: 12px; background: #fff; color: #657786; text-align: center; box-shadow: 0 20px 60px rgba(17,27,43,.24); }
    .structure-user-modal > .card { width: min(100%, 760px); }
    .structure-user-modal-error { width: min(100%, 620px); padding: 24px; border-radius: 12px; background: #fff; color: #a72d2d; box-shadow: 0 20px 60px rgba(17,27,43,.24); }
    .structure-node-actions { display: inline-flex; align-items: center; gap: 4px; margin-left: 10px; opacity: 0; transition: opacity .12s ease; }
    .structure-node-row:hover .structure-node-actions { opacity: 1; }
    .structure-icon-button { display: grid; place-items: center; width: 27px; height: 27px; min-height: 27px; padding: 0 !important; border: 1px solid #cbdad5 !important; border-radius: 6px; background: #fff !important; color: #526778 !important; }
    .structure-icon-button:hover { border-color: #9dcac0 !important; background: #edf7f4 !important; color: #0f766e !important; }
    .structure-icon-button.danger-outline { border-color: #edc1c1 !important; color: #c43c3c !important; }
    .structure-icon-button.danger-outline:hover { background: #fff2f2 !important; }
    .structure-children { position: relative; margin-left: 39px !important; padding-left: 26px !important; border-left: 1px solid #d7dfe5; }
    .structure-children > .structure-node::before { content: ''; position: absolute; left: -26px; top: 24px; width: 21px; border-top: 1px solid #d7dfe5; }
    .structure-children > .structure-node:last-child::after { content: ''; position: absolute; left: -27px; top: 25px; bottom: 0; width: 3px; background: #fff; }
    .structure-empty { padding: 70px 20px; color: #7c8a95; text-align: center; }
    .structure-empty i { display: block; margin-bottom: 10px; font-size: 34px; color: #aab5bd; }
    .structure-editor-grid { display: grid; gap: 13px; }
    .structure-editor-grid label { display: grid; gap: 6px; color: #405467; font-size: 13px; font-weight: 700; }
    .structure-editor-grid input[type="text"], .structure-editor-grid select { width: 100%; min-height: 40px; padding: 8px 11px; border: 1px solid #cbdad5; border-radius: 7px; background: #fff; color: #223047; font: inherit; font-size: 13px; font-weight: 400; }
    .structure-group-checkbox { display: flex !important; align-items: center; gap: 9px !important; }
    .structure-group-checkbox input { width: 16px; height: 16px; margin: 0; accent-color: #0f766e; }
    .structure-form-hint { color: #7a8790; font-size: 11px; font-weight: 400; line-height: 1.35; }
    .structure-errors { margin-bottom: 12px; padding: 10px 12px; border: 1px solid #f0baba; border-radius: 7px; background: #fff3f3; color: #a72d2d; font-size: 12px; }
    .structure-errors ul { margin: 0; padding-left: 18px; }
    .structure-editor-grid .select2-container { width: 100% !important; font-weight: 400; }
    .structure-editor-grid .select2-container--default .select2-selection--multiple { min-height: 40px; padding: 5px 8px; border-color: #cbdad5; border-radius: 7px; }
    .structure-editor-grid .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #0f766e; }
    .structure-editor-grid .select2-container--default .select2-selection--multiple .select2-selection__rendered { display: flex; flex-wrap: wrap; align-items: center; gap: 4px; width: 100%; padding: 0 !important; margin: 0 !important; }
    .structure-editor-grid .select2-container--default .select2-selection--multiple .select2-selection__choice { position: relative; display: inline-flex; align-items: center; min-height: 26px; margin: 0 !important; padding: 2px 8px 2px 25px !important; border-color: #bcd8d1; border-radius: 5px; background: #edf7f4; color: #315b55; font-size: 12px; font-weight: 400; line-height: 20px; }
    .structure-editor-grid .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { position: absolute; left: 0; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; width: 20px; margin: 0 !important; padding: 0 !important; border: 0; border-right: 1px solid #bcd8d1; border-radius: 5px 0 0 5px; color: #6b7d78; font-size: 14px; line-height: 1; }
    .structure-editor-grid .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { background: #dceee9; color: #24564f; }
    .structure-editor-grid .select2-container--default .select2-search--inline { display: inline-flex; align-items: center; min-height: 26px; margin: 0; }
    .structure-editor-grid .select2-container--default .select2-search--inline .select2-search__field { height: 26px; min-height: 26px; margin: 0 !important; padding: 2px 0 !important; font: inherit; font-size: 12px; font-weight: 400; line-height: 22px; }
    @media (max-width: 760px) {
        .structure-node-actions { opacity: 1; }
        .structure-children { margin-left: 24px !important; padding-left: 18px !important; }
    }
</style>
@endpush

@section('content')
<x-catalog-page title="Структура компании">
    <x-flash-status />

    {{-- Используем те же классы и иконки, что и общий x-data-table. --}}
    <div class="data-table-tools" id="structure-catalog-tools">
        <div class="table-search">
            <i data-lucide="search"></i>
            <input
                id="structure-search"
                type="search"
                placeholder="Поиск по должности или сотруднику"
                aria-label="Поиск по структуре компании"
                autocomplete="off"
            >
            <span class="data-table-count" id="structure-visible-count">Элементов: {{ $allNodes->count() }}</span>
            <button
                class="table-filter-button"
                type="button"
                aria-label="Фильтр"
                title="Фильтр"
            >
                <i data-lucide="funnel"></i>
            </button>
        </div>

        <div class="table-filter-status" id="structure-filter-status" hidden>
            <i data-lucide="funnel"></i>
            <span></span>
            <button
                type="button"
                class="clear-active-filter"
                aria-label="Сбросить фильтр"
                title="Сбросить фильтр"
            >×</button>
        </div>

        @if($canEdit)
            <button
                id="add-root-node"
                class="button btn btn-primary create-user-button create-icon-button"
                type="button"
                title="Создать узел"
            >
                <i class="ti ti-plus"></i>
                <span class="d-none d-sm-inline">Создать узел</span>
            </button>
        @endif

        <div class="table-menu">
            <button
                class="table-menu-button"
                type="button"
                aria-label="Действия со структурой"
                title="Действия со структурой"
            >
                <i data-lucide="settings-2"></i>
            </button>

            <div class="table-actions-menu" hidden>
                <button id="structure-export-excel" type="button">
                    <i data-lucide="download"></i>
                    Выгрузить в Excel
                </button>
                <button id="structure-expand-all" type="button">
                    <i data-lucide="chevrons-down"></i>
                    Развернуть всё
                </button>
                <button id="structure-collapse-all" type="button">
                    <i data-lucide="chevrons-up"></i>
                    Свернуть всё
                </button>
            </div>
        </div>
    </div>

    <x-modal
        id="structure-filter-overlay"
        class="table-filter-overlay"
        dialog-class="table-filter-panel"
        title="Фильтр"
        :hidden="true"
        close-button-class="close-filter"
    >
        <div class="filter-fields">
            <label>
                Показывать
                <select class="filter-value" id="structure-type-filter">
                    <option value="all">Все узлы</option>
                    <option value="with-users">С сотрудниками</option>
                    <option value="without-users">Без сотрудников</option>
                    <option value="groups">Групповые должности</option>
                </select>
            </label>
        </div>

        <x-slot:actions>
            <div class="filter-actions">
                <button class="apply-filter" id="structure-filter-apply" type="button">
                    <i data-lucide="check"></i>
                    Применить
                </button>
                <button class="reset-filter" id="structure-filter-reset" type="button">
                    <i data-lucide="rotate-ccw"></i>
                    Сбросить
                </button>
                <button class="cancel-filter" id="structure-filter-cancel" type="button">
                    <i data-lucide="x"></i>
                    Отмена
                </button>
            </div>
        </x-slot:actions>
    </x-modal>

    <div class="structure-tree-card">
        @if($nodes->isEmpty())
            <div class="structure-empty">
                <i class="ti ti-sitemap"></i>
                Структура компании пока пуста.
                @if($canEdit)
                    <div class="mt-2">Нажмите «Создать узел», чтобы создать первый уровень.</div>
                @endif
            </div>
        @else
            <ul class="structure-tree" id="company-structure-tree">
                @foreach($nodes as $node)
                    @include('company-structure.partials.node', ['node' => $node, 'canEdit' => $canEdit])
                @endforeach
            </ul>
        @endif
    </div>
</x-catalog-page>


<div id="structure-user-modal" class="modal table-record-modal structure-user-modal" hidden aria-hidden="true">
    <div class="structure-user-modal-loading">Загрузка карточки пользователя...</div>
</div>

@if($canEdit)
<x-modal id="structure-editor-modal" variant="editor" title="Добавить узел" hidden close-button-id="structure-editor-close">
    @if($errors->any())
        <div class="structure-errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="structure-editor-form" method="POST" action="{{ route('workspace.company-structure.nodes.store') }}">
        @csrf
        <input id="structure-method" type="hidden" name="_method" value="">
        <input id="structure-editor-mode" type="hidden" name="_editor_mode" value="create">
        <input id="structure-editor-node-id" type="hidden" name="_node_id" value="">

        <div class="structure-editor-grid">
            <label>
                <span>Название должности или узла <em>*</em></span>
                <input id="structure-title" type="text" name="title" value="{{ old('title') }}" required maxlength="255">
            </label>

            <label>
                <span>Родительский узел</span>
                <select id="structure-parent" name="parent_id">
                    <option value="">Верхний уровень</option>
                    @foreach($nodeOptions as $option)
                        <option value="{{ $option['id'] }}" data-node-option="{{ $option['id'] }}" data-parent-id="{{ $option['parent_id'] }}">
                            {{ str_repeat('— ', $option['depth']) }}{{ $option['title'] }}
                        </option>
                    @endforeach
                </select>
                <span class="structure-form-hint">Можно создавать отделы без сотрудников и вкладывать в них должности.</span>
            </label>

            <label class="structure-group-checkbox">
                <input id="structure-multiple" type="checkbox" name="allows_multiple_users" value="1">
                <span>Групповая должность</span>
            </label>
            <div class="structure-form-hint" id="structure-multiple-hint">Для обычной должности можно выбрать одного сотрудника.</div>

            <label>
                <span>Сотрудники</span>
                <select id="structure-users" class="app-user-select" name="user_ids[]" multiple>
                    @foreach($users as $user)
                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                    @endforeach
                </select>
                <span class="structure-form-hint">Сотрудника можно не назначать. Для групповой должности можно выбрать несколько человек.</span>
            </label>
        </div>
    </form>

    <x-slot:actions>
        <button id="structure-editor-submit" type="submit" form="structure-editor-form">Создать</button>
        <button class="secondary" id="structure-editor-cancel" type="button">Отмена</button>
    </x-slot:actions>
</x-modal>

<form id="structure-delete-form" method="POST" hidden>
    @csrf
    @method('DELETE')
</form>
@endif
@endsection

@push('scripts')
<script>
(() => {
    const collapsedStorageKey = 'company-structure-collapsed-v3';
    const tree = document.getElementById('company-structure-tree');
    const treeCard = document.querySelector('.structure-tree-card');
    const tools = document.getElementById('structure-catalog-tools');
    const search = document.getElementById('structure-search');
    const visibleCount = document.getElementById('structure-visible-count');

    const filterButton = tools?.querySelector('.table-filter-button');
    const filterOverlay = document.getElementById('structure-filter-overlay');
    const filterPanel = filterOverlay?.querySelector('.table-filter-panel');
    const filterStatus = document.getElementById('structure-filter-status');
    const clearActiveFilter = filterStatus?.querySelector('.clear-active-filter');
    const typeFilter = document.getElementById('structure-type-filter');
    const filterApply = document.getElementById('structure-filter-apply');
    const filterReset = document.getElementById('structure-filter-reset');
    const filterCancel = document.getElementById('structure-filter-cancel');

    const menu = tools?.querySelector('.table-menu');
    const menuButton = menu?.querySelector('.table-menu-button');
    const actionsMenu = menu?.querySelector('.table-actions-menu');
    const exportButton = document.getElementById('structure-export-excel');
    const expandAllButton = document.getElementById('structure-expand-all');
    const collapseAllButton = document.getElementById('structure-collapse-all');

    let activeTypeFilter = 'all';

    /*
     * Белая область дерева должна доходить почти до нижнего края окна.
     * Считаем высоту от её фактической позиции, чтобы это не зависело
     * от высоты шапки, масштаба браузера и размера экрана.
     */
    function fitTreeCardToViewport() {
        if (!treeCard) return;

        const top = treeCard.getBoundingClientRect().top;
        const bottomGap = 18;
        const minHeight = Math.max(520, Math.floor(window.innerHeight - top - bottomGap));

        treeCard.style.minHeight = `${minHeight}px`;
    }

    /*
     * Ровно как CatalogTable::placeToolsInHeading():
     * поиск и фильтр ставятся после заголовка, кнопка создания остаётся справа,
     * меню действий становится последним элементом стандартной шапки.
     */
    function placeToolsInHeading() {
        if (!tools) return;

        const heading = tools.closest('.catalog-page')?.querySelector('.catalog-heading');
        if (!heading) return;

        const create = tools.querySelector('.create-icon-button');
        const searchBox = tools.querySelector('.table-search');

        [searchBox, filterStatus, create, menu]
            .filter(Boolean)
            .forEach(element => heading.append(element));

        tools.hidden = true;
    }

    function expandableNodes() {
        return Array.from(document.querySelectorAll('.structure-node')).filter(li =>
            !!li.querySelector(':scope > .structure-children')
        );
    }

    function defaultCollapsedState() {
        const state = {};

        expandableNodes().forEach(li => {
            state[String(li.dataset.nodeId)] = true;
        });

        /*
         * Начальное состояние: раскрыт только первый корневой узел.
         * Его непосредственные дети видны, их ветки дальше уже свернуты.
         */
        const firstRoot = tree?.querySelector(':scope > .structure-node');

        if (firstRoot && firstRoot.querySelector(':scope > .structure-children')) {
            state[String(firstRoot.dataset.nodeId)] = false;
        }

        return state;
    }

    function loadCollapsed() {
        try {
            const raw = localStorage.getItem(collapsedStorageKey);
            return raw ? (JSON.parse(raw) || {}) : defaultCollapsedState();
        } catch (_) {
            return defaultCollapsedState();
        }
    }

    function saveCollapsed(state) {
        localStorage.setItem(collapsedStorageKey, JSON.stringify(state));
    }

    let collapsed = loadCollapsed();

    function applyCollapsedState() {
        document.querySelectorAll('.structure-node').forEach(li => {
            const id = String(li.dataset.nodeId || '');

            if (!li.querySelector(':scope > .structure-children')) {
                li.classList.remove('is-collapsed');
                return;
            }

            li.classList.toggle('is-collapsed', !!collapsed[id]);
        });
    }

    function rowMatchesType(row) {
        if (activeTypeFilter === 'with-users') return row?.dataset.hasUsers === '1';
        if (activeTypeFilter === 'without-users') return row?.dataset.hasUsers !== '1';
        if (activeTypeFilter === 'groups') return row?.dataset.isGroup === '1';
        return true;
    }

    function applyTreeFilter() {
        const q = (search?.value || '').trim().toLowerCase();
        const allNodes = Array.from(document.querySelectorAll('.structure-node'));

        if (!q && activeTypeFilter === 'all') {
            allNodes.forEach(li => { li.hidden = false; });
            applyCollapsedState();

            if (visibleCount) {
                visibleCount.textContent = `Элементов: ${allNodes.length}`;
            }

            return;
        }

        let matchingOwnNodes = 0;

        allNodes.slice().reverse().forEach(li => {
            const row = li.querySelector(':scope > .structure-node-row');
            const textMatches = !q || (row?.dataset.searchText || '').includes(q);
            const ownMatch = textMatches && rowMatchesType(row);
            const childMatch = Array.from(
                li.querySelectorAll(':scope > .structure-children > .structure-node')
            ).some(child => !child.hidden);

            if (ownMatch) {
                matchingOwnNodes++;
            }

            li.hidden = !(ownMatch || childMatch);

            if (!li.hidden) {
                li.classList.remove('is-collapsed');
            }
        });

        if (visibleCount) {
            visibleCount.textContent = `Элементов: ${matchingOwnNodes}`;
        }
    }

    function filterLabel() {
        if (!typeFilter) return '';

        return typeFilter.options[typeFilter.selectedIndex]?.text?.trim() || '';
    }

    function updateFilterStatus() {
        if (!filterStatus) return;

        filterStatus.hidden = activeTypeFilter === 'all';

        const label = filterStatus.querySelector('span');
        if (label && !filterStatus.hidden) {
            label.textContent = `Фильтр: Показывать: ${filterLabel()}`;
        }
    }

    function closeFilter() {
        if (filterOverlay) {
            filterOverlay.hidden = true;
        }
    }

    function closeActionsMenu() {
        if (actionsMenu) {
            actionsMenu.hidden = true;
        }
    }

    function expandAll() {
        expandableNodes().forEach(li => {
            collapsed[String(li.dataset.nodeId)] = false;
        });

        saveCollapsed(collapsed);
        applyTreeFilter();
        closeActionsMenu();
    }

    function collapseAll() {
        expandableNodes().forEach(li => {
            collapsed[String(li.dataset.nodeId)] = true;
        });

        saveCollapsed(collapsed);
        applyTreeFilter();
        closeActionsMenu();
    }

    placeToolsInHeading();
    fitTreeCardToViewport();
    applyCollapsedState();

    /*
     * До этого момента дерево скрыто через visibility, поэтому браузер
     * не успевает показать полностью раскрытую структуру перед тем, как
     * применится сохранённое состояние сворачивания.
     */
    tree?.classList.add('is-ready');

    updateFilterStatus();

    window.addEventListener('resize', fitTreeCardToViewport);

    document.querySelectorAll('[data-structure-toggle]').forEach(button => {
        const id = String(button.dataset.structureToggle || '');
        const li = document.querySelector(`.structure-node[data-node-id="${CSS.escape(id)}"]`);

        if (!li) return;

        button.addEventListener('click', () => {
            li.classList.toggle('is-collapsed');
            collapsed[id] = li.classList.contains('is-collapsed');
            saveCollapsed(collapsed);
        });
    });

    search?.addEventListener('input', applyTreeFilter);

    filterButton?.addEventListener('click', () => {
        if (!filterOverlay) return;
        filterOverlay.hidden = false;
        closeActionsMenu();
    });

    filterOverlay?.addEventListener('click', event => {
        if (event.target === filterOverlay) {
            closeFilter();
        }
    });

    filterPanel?.querySelector('.close-filter')?.addEventListener('click', closeFilter);
    filterCancel?.addEventListener('click', closeFilter);

    filterApply?.addEventListener('click', () => {
        activeTypeFilter = typeFilter?.value || 'all';
        updateFilterStatus();
        applyTreeFilter();
        closeFilter();
    });

    filterReset?.addEventListener('click', () => {
        if (typeFilter) typeFilter.value = 'all';
        activeTypeFilter = 'all';
        updateFilterStatus();
        applyTreeFilter();
        closeFilter();
    });

    clearActiveFilter?.addEventListener('click', () => {
        if (typeFilter) typeFilter.value = 'all';
        activeTypeFilter = 'all';
        updateFilterStatus();
        applyTreeFilter();
    });

    menuButton?.addEventListener('click', event => {
        event.stopPropagation();

        if (actionsMenu) {
            actionsMenu.hidden = !actionsMenu.hidden;
        }
    });

    actionsMenu?.addEventListener('click', event => event.stopPropagation());

    document.addEventListener('pointerdown', event => {
        if (
            actionsMenu &&
            menuButton &&
            !actionsMenu.contains(event.target) &&
            !menuButton.contains(event.target)
        ) {
            actionsMenu.hidden = true;
        }
    });

    exportButton?.addEventListener('click', () => {
        closeActionsMenu();
        window.location.href = @json(route('workspace.company-structure.export'));
    });

    expandAllButton?.addEventListener('click', expandAll);
    collapseAllButton?.addEventListener('click', collapseAll);


    const userInfoModal = document.getElementById('structure-user-modal');

    function closeStructureUserCard() {
        if (!userInfoModal) return;
        userInfoModal.hidden = true;
        userInfoModal.setAttribute('aria-hidden', 'true');
        userInfoModal.innerHTML = '<div class="structure-user-modal-loading">Загрузка карточки пользователя...</div>';
    }

    function bindStructureUserCardCloseButtons() {
        if (!userInfoModal) return;

        userInfoModal.querySelectorAll('.modal-close, .table-modal-close, [data-modal-close]').forEach(control => {
            /*
             * Карточка пользователя загружается из справочника "Пользователи".
             * Там кнопка "Закрыть" может быть ссылкой обратно на список пользователей.
             * В оргструктуре навигация нам не нужна: превращаем такую ссылку в обычную кнопку.
             */
            let button = control;

            if (control.tagName === 'A') {
                const replacement = document.createElement('button');
                replacement.type = 'button';
                replacement.className = control.className;
                replacement.innerHTML = control.innerHTML;

                ['title', 'aria-label'].forEach(attribute => {
                    if (control.hasAttribute(attribute)) {
                        replacement.setAttribute(attribute, control.getAttribute(attribute));
                    }
                });

                control.replaceWith(replacement);
                button = replacement;
            }

            button.removeAttribute('href');
            button.removeAttribute('onclick');
            button.setAttribute('type', 'button');
            button.setAttribute('data-structure-user-modal-close', '1');
        });
    }

    async function openStructureUserCard(url) {
        if (!userInfoModal || !url) return;

        userInfoModal.hidden = false;
        userInfoModal.setAttribute('aria-hidden', 'false');
        userInfoModal.innerHTML = '<div class="structure-user-modal-loading">Загрузка карточки пользователя...</div>';

        try {
            const response = await fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const html = await response.text();
            const documentFromResponse = new DOMParser().parseFromString(html, 'text/html');
            const sourceCard =
                documentFromResponse.querySelector('.table-record-modal .card') ||
                documentFromResponse.querySelector('.modal .card') ||
                documentFromResponse.querySelector('.card');

            if (!sourceCard) {
                throw new Error('Карточка пользователя не найдена в ответе сервера.');
            }

            userInfoModal.innerHTML = '';
            userInfoModal.appendChild(document.importNode(sourceCard, true));
            bindStructureUserCardCloseButtons();
        } catch (error) {
            console.error('Не удалось открыть карточку пользователя', error);
            userInfoModal.innerHTML = `
                <div class="structure-user-modal-error">
                    Не удалось открыть карточку пользователя.
                    <div style="margin-top:14px;">
                        <button type="button" class="secondary" data-structure-user-modal-close>Закрыть</button>
                    </div>
                </div>
            `;
            userInfoModal.querySelector('[data-structure-user-modal-close]')?.addEventListener('click', closeStructureUserCard);
        }
    }

    document.querySelectorAll('[data-structure-user-card]').forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            openStructureUserCard(link.href);
        });
    });

    /*
     * Перехватываем закрытие в capture-фазе. Это важно, потому что общая логика
     * модальных окон справочника пользователей может иметь свой обработчик,
     * который после закрытия отправляет на /users. До него событие теперь не доходит.
     */
    userInfoModal?.addEventListener('click', event => {
        const closeControl = event.target.closest(
            '.modal-close, .table-modal-close, [data-modal-close], [data-structure-user-modal-close]'
        );

        if (closeControl) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            closeStructureUserCard();
            return;
        }

        if (event.target === userInfoModal) {
            event.preventDefault();
            event.stopPropagation();
            closeStructureUserCard();
        }
    }, true);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && userInfoModal && !userInfoModal.hidden) {
            closeStructureUserCard();
        }
    });

    @if($canEdit)
    const modal = document.getElementById('structure-editor-modal');
    const form = document.getElementById('structure-editor-form');
    const methodInput = document.getElementById('structure-method');
    const modeInput = document.getElementById('structure-editor-mode');
    const nodeIdInput = document.getElementById('structure-editor-node-id');
    const titleInput = document.getElementById('structure-title');
    const parentSelect = document.getElementById('structure-parent');
    const multipleCheckbox = document.getElementById('structure-multiple');
    const usersSelect = document.getElementById('structure-users');
    const multipleHint = document.getElementById('structure-multiple-hint');
    const submitButton = document.getElementById('structure-editor-submit');
    const modalTitle = modal?.querySelector('.app-modal__header h2');
    const storeUrl = @json(route('workspace.company-structure.nodes.store'));
    const updateBaseUrl = @json(url('/company-structure/nodes'));
    const deleteBaseUrl = @json(url('/company-structure/nodes'));
    const nodeParents = @json($allNodes->mapWithKeys(fn($n) => [(string) $n->id => $n->parent_id ? (string) $n->parent_id : null]));

    function isDescendant(candidateId, nodeId) {
        let current = candidateId ? String(candidateId) : null;
        const target = nodeId ? String(nodeId) : null;
        while (current) {
            if (current === target) return true;
            current = nodeParents[current] || null;
        }
        return false;
    }

    function refreshParentOptions(editingNodeId) {
        parentSelect.querySelectorAll('option[data-node-option]').forEach(option => {
            option.disabled = !!editingNodeId && isDescendant(option.value, editingNodeId);
        });
    }

    function refreshMultipleMode() {
        const multiple = multipleCheckbox.checked;
        multipleHint.textContent = multiple
            ? 'Можно выбрать несколько сотрудников.'
            : 'Для обычной должности можно выбрать одного сотрудника.';

        if (!multiple) {
            const selected = $('#structure-users').val() || [];
            if (selected.length > 1) {
                $('#structure-users').val([selected[selected.length - 1]]).trigger('change');
            }
        }
    }

    function setSelectedUsers(ids) {
        $('#structure-users').val((ids || []).map(String)).trigger('change');
    }

    function openEditor(data = {}) {
        const isEdit = data.mode === 'edit';
        const nodeId = isEdit ? String(data.id || '') : '';
        form.action = isEdit ? `${updateBaseUrl}/${encodeURIComponent(nodeId)}` : storeUrl;
        methodInput.value = isEdit ? 'PUT' : '';
        modeInput.value = isEdit ? 'edit' : 'create';
        nodeIdInput.value = nodeId;
        titleInput.value = data.title || '';
        parentSelect.value = data.parentId ? String(data.parentId) : '';
        multipleCheckbox.checked = !!data.allowsMultiple;
        setSelectedUsers(data.userIds || []);
        refreshParentOptions(nodeId);
        refreshMultipleMode();
        if (modalTitle) modalTitle.textContent = isEdit ? 'Редактировать узел' : 'Добавить узел';
        submitButton.textContent = isEdit ? 'Сохранить' : 'Создать';
        modal.hidden = false;
        setTimeout(() => titleInput.focus(), 0);
    }

    function closeEditor() {
        modal.hidden = true;
    }

    $('#structure-users').select2({
        placeholder: 'Выберите сотрудника',
        closeOnSelect: false,
        width: '100%',
        dropdownParent: $('#structure-editor-modal .app-modal__dialog'),
    });

    $('#structure-users').on('change', () => {
        if (!multipleCheckbox.checked) {
            const selected = $('#structure-users').val() || [];
            if (selected.length > 1) {
                $('#structure-users').val([selected[selected.length - 1]]).trigger('change');
            }
        }
    });

    multipleCheckbox.addEventListener('change', refreshMultipleMode);
    document.getElementById('add-root-node')?.addEventListener('click', () => openEditor({ mode: 'create' }));
    document.getElementById('structure-editor-close')?.addEventListener('click', closeEditor);
    document.getElementById('structure-editor-cancel')?.addEventListener('click', closeEditor);

    modal?.addEventListener('click', event => {
        if (event.target === modal) closeEditor();
    });

    document.querySelectorAll('[data-add-child]').forEach(button => {
        button.addEventListener('click', () => openEditor({ mode: 'create', parentId: button.dataset.addChild }));
    });

    document.querySelectorAll('[data-edit-node]').forEach(button => {
        button.addEventListener('click', () => {
            openEditor({
                mode: 'edit',
                id: button.dataset.editNode,
                title: button.dataset.nodeTitle || '',
                parentId: button.dataset.parentId || '',
                allowsMultiple: button.dataset.allowsMultiple === '1',
                userIds: JSON.parse(button.dataset.userIds || '[]'),
            });
        });
    });

    document.querySelectorAll('[data-delete-node]').forEach(button => {
        button.addEventListener('click', () => {
            const title = button.dataset.nodeTitle || 'этот узел';
            if (!confirm(`Удалить «${title}» вместе со всеми дочерними узлами?`)) return;
            const deleteForm = document.getElementById('structure-delete-form');
            deleteForm.action = `${deleteBaseUrl}/${encodeURIComponent(button.dataset.deleteNode)}`;
            deleteForm.submit();
        });
    });

    @if($errors->any())
        openEditor({
            mode: @json(old('_editor_mode', 'create')),
            id: @json(old('_node_id')),
            title: @json(old('title', '')),
            parentId: @json(old('parent_id')),
            allowsMultiple: @json((bool) old('allows_multiple_users')),
            userIds: @json(old('user_ids', [])),
        });
    @endif
    @endif
})();
</script>
@endpush
