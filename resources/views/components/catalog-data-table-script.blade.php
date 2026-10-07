@verbatim
<script>
/**
 * Единственный клиентский модуль для <x-data-table>.
 * Страница справочника задаёт только колонки и строки; поведение находится здесь.
 */
(() => {
    'use strict';

    const htmlToText = (html) => {
        const container = document.createElement('div');
        container.innerHTML = html ?? '';
        container.querySelectorAll('.user-avatar').forEach((avatar) => avatar.remove());
        return container.textContent.trim();
    };

    class CatalogTable {
        constructor(root) {
            this.root = root;
            this.config = JSON.parse(root.dataset.catalogConfig);
            this.grid = root.querySelector('[data-table-grid]');
            this.$ = window.jQuery;
            this.widthStorageKey = `catalog-table-resize-widths:${this.config.id}`;
            this.widths = this.readStorage(this.widthStorageKey);
            this.el = {
                search: root.querySelector('.table-search input'),
                count: root.querySelector('.data-table-count'),
                filterButton: root.querySelector('.table-filter-button'),
                actions: root.querySelector('.table-actions-menu'),
                menuButton: root.querySelector('.table-menu-button'),
                settingsButton: root.querySelector('.table-settings'),
                exportButton: root.querySelector('.export-excel'),
                settings: root.querySelector('.table-settings-panel'),
                settingsClose: root.querySelector('.close-table-settings'),
                settingsList: root.querySelector('.column-settings-list'),
                filterOverlay: root.querySelector('.table-filter-overlay'),
                filter: root.querySelector('.table-filter-panel'),
                filterStatus: root.querySelector('.table-filter-status'),
                bulk: root.querySelector('.table-bulk-delete'),
            };
        }

        readStorage(key) {
            try { return JSON.parse(localStorage.getItem(key) || '{}'); } catch { return {}; }
        }

        init() {
            if (!this.$?.fn.DataTable) return;
            this.placeToolsInHeading();
            this.createTable();
            this.restoreWidths();
            this.bindFilters();
            this.bindMenu();
            this.bindSettings();
            this.bindResize();
            this.bindExport();
            this.bindSelection();
            this.bindBackToTop();
            this.bindRemoteModals();
            this.revealGrid();
        }

        revealGrid() {
            // DataTables finishes creating colgroups just after initialisation.
            // Keep the grid hidden until its measured widths are stable.
            requestAnimationFrame(() => requestAnimationFrame(() => {
                this.table.columns.adjust().draw(false);
                // Let the browser finish the DataTables redraw before revealing it.
                setTimeout(() => this.root.classList.remove('is-initializing'), 80);
            }));
        }

        createTable() {
            const headers = [...this.grid.tHead.rows[0].cells];
            const isReference = this.config.kind === 'reference';
            const columns = headers.map((header, index) => header.dataset.column ? {
                targets: index,
                name: header.dataset.column,
                visible: this.config.defaultVisible.includes(header.dataset.column),
            } : null).filter(Boolean);

            this.table = this.$(this.grid).DataTable({
                stateSave: !isReference,
                stateDuration: -1,
                colReorder: { enable: false },
                // Таблица прокручивается вместе со страницей, а шапка закреплена CSS.
                scrollX: false,
                scrollY: '',
                scrollCollapse: false,
                paging: false,
                deferRender: true,
                order: [],
                columnDefs: [
                    ...(this.config.hasBulkDelete ? [{ targets: 0, orderable: false, searchable: false, width: '46px' }] : []),
                    ...columns,
                    ...(isReference ? [{ targets: 1, width: '42%' }, { targets: 2, width: '27%' }, { targets: 4, width: '25%' }] : []),
                ],
                layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null },
                language: { emptyTable: this.config.emptyMessage, info: 'Показаны _START_–_END_ из _TOTAL_', infoEmpty: 'Нет записей', zeroRecords: 'Ничего не найдено' },
            });

            if (isReference) {
                ['name', 'creator', 'updated'].forEach((name) => this.table.column(`${name}:name`).visible(true, false));
                ['created', 'editor'].forEach((name) => this.table.column(`${name}:name`).visible(false, false));
                this.table.columns.adjust().draw(false);
            }
            this.table.on('draw.dt', () => this.updateCount());
            this.updateCount();
        }

        updateCount() {
            if (this.el.count) this.el.count.textContent = `Элементов: ${this.table.page.info().recordsDisplay}`;
        }

        placeToolsInHeading() {
            const heading = this.root.closest('.catalog-page')?.querySelector('.catalog-heading');
            if (!heading) return;
            const create = heading.querySelector('.create-icon-button');
            const search = this.root.querySelector('.table-search');
            const status = this.el.filterStatus;
            const bulk = this.root.querySelector('.table-bulk-delete');
            const menu = this.root.querySelector('.table-menu');
            [search, status].filter(Boolean).forEach((element) => heading.insertBefore(element, create));
            if (create && bulk) create.after(bulk);
            if (menu) {
                heading.append(menu);
                menu.append(this.el.settings);
            }
            this.root.querySelector('.data-table-tools').hidden = true;
        }

        filterValue(input) {
            if (!input.value) return '';
            if (input.matches('select')) return input.options[input.selectedIndex].text.trim();
            return input.type === 'date' ? input.value.split('-').reverse().join('.') : input.value.trim();
        }

        bindFilters() {
            const { search, filterOverlay, filter, filterStatus, actions, settings } = this.el;
            const inputs = () => [...filter.querySelectorAll('.filter-value')];
            this.$(filter).find('.app-user-select').each((_, element) => {
                const select = this.$(element);
                if (select.hasClass('select2-hidden-accessible')) return;
                select.select2({
                    width: '100%',
                    placeholder: 'Любое значение',
                    allowClear: true,
                    dropdownParent: this.$(filterOverlay),
                });
            });
            const active = () => inputs().map((input) => ({ input, value: this.filterValue(input) })).filter(({ value }) => value);
            const updateStatus = () => {
                const filters = active().map(({ input, value }) => `${input.closest('label').childNodes[0].textContent.trim()}: ${value}`);
                filterStatus.hidden = !filters.length;
                filterStatus.querySelector('span').textContent = filters.length === 1 ? filters[0] : `Фильтры: ${filters.length}`;
            };
            const clear = () => {
                inputs().forEach((input) => { input.value = ''; this.$(input).trigger('change.select2'); });
                this.table.search('');
                this.table.columns().every(function clearColumnFilter() { this.search(''); });
                this.table.draw(); updateStatus();
            };

            search.oninput = (event) => this.table.search(event.target.value).draw();
            this.el.filterButton.onclick = () => { filterOverlay.hidden = false; actions.hidden = true; settings.hidden = true; };
            filterOverlay.onclick = (event) => { if (event.target === filterOverlay) filterOverlay.hidden = true; };
            filter.querySelector('.close-filter').onclick = () => { filterOverlay.hidden = true; };
            filter.querySelector('.cancel-filter').onclick = () => { filterOverlay.hidden = true; };
            filter.querySelector('.apply-filter').onclick = () => {
                // Сначала запоминаем условия. clear() очищает поля формы,
                // поэтому читать active() после него было нельзя.
                const filters = active();
                this.table.search('');
                this.table.columns().every(function clearColumnFilter() { this.search(''); });
                filters.forEach(({ input, value }) => this.table.column(`${input.dataset.filterKey}:name`).search(value));
                this.table.draw(); updateStatus(); filterOverlay.hidden = true;
            };
            filter.querySelector('.reset-filter').onclick = () => { clear(); filterOverlay.hidden = true; };
            filterStatus.querySelector('.clear-active-filter').onclick = clear;
        }

        bindMenu() {
            this.el.menuButton.onclick = () => { this.el.actions.hidden = !this.el.actions.hidden; };
            document.addEventListener('pointerdown', (event) => {
                if (!this.el.actions.contains(event.target) && !this.el.menuButton.contains(event.target)) this.el.actions.hidden = true;
                if (!this.el.settings.contains(event.target) && !this.el.settingsButton.contains(event.target)) this.el.settings.hidden = true;
            });
        }

        bindSettings() {
            this.el.settingsClose.onclick = () => { this.el.settings.hidden = true; };
            this.el.settingsButton.onclick = () => {
                this.el.actions.hidden = true;
                this.el.settings.hidden = !this.el.settings.hidden;
                if (!this.el.settings.hidden) this.renderSettings();
            };
            this.el.settings.querySelector('.reset-columns').onclick = () => {
                localStorage.removeItem(this.widthStorageKey);
                location.reload();
            };
        }

        renderSettings() {
            const list = this.el.settingsList;
            list.innerHTML = '';
            const clearDropMarker = () => list.querySelectorAll('.is-drop-before,.is-drop-after').forEach((item) => item.classList.remove('is-drop-before', 'is-drop-after'));
            [...this.grid.tHead.rows[0].cells].forEach((header) => {
                if (!header.dataset.column) return;
                const key = header.dataset.column;
                const column = this.table.column(header);
                const item = document.createElement('li');
                item.draggable = Boolean(this.table.colReorder);
                item.dataset.column = key;
                item.innerHTML = `<i data-lucide="grip-vertical" class="column-order-handle" title="Перетащить столбец"></i><label><input type="checkbox" ${column.visible() ? 'checked' : ''}> ${this.$(column.header()).text().trim()}</label>`;
                item.querySelector('input').onchange = (event) => { column.visible(event.target.checked); this.table.columns.adjust().draw(false); };
                item.ondragstart = (event) => { event.dataTransfer.setData('text/plain', key); item.classList.add('is-dragging'); };
                item.ondragend = () => { item.classList.remove('is-dragging'); clearDropMarker(); };
                item.ondragover = (event) => { event.preventDefault(); clearDropMarker(); item.classList.add(event.clientY < item.getBoundingClientRect().top + item.offsetHeight / 2 ? 'is-drop-before' : 'is-drop-after'); };
                item.ondrop = (event) => this.moveColumn(event, item, clearDropMarker);
                list.append(item);
            });
            window.lucide?.createIcons();
        }

        moveColumn(event, target, clearDropMarker) {
            event.preventDefault();
            const sourceKey = event.dataTransfer.getData('text/plain');
            const targetKey = target.dataset.column;
            const after = target.classList.contains('is-drop-after');
            clearDropMarker();
            if (!sourceKey || sourceKey === targetKey || !this.table.colReorder) return;
            const order = this.table.colReorder.order();
            const source = order.indexOf(this.table.column(`${sourceKey}:name`).index());
            let destination = order.indexOf(this.table.column(`${targetKey}:name`).index()) + (after ? 1 : 0);
            const moved = order.splice(source, 1)[0];
            if (source < destination) destination--;
            order.splice(destination, 0, moved);
            this.table.colReorder.order(order, false);
            this.table.columns.adjust().draw(false);
            this.renderSettings();
        }

        headerFor(key) { return [...this.grid.tHead.rows[0].cells].find((header) => header.dataset.column === key); }

        setWidth(header, width) {
            const normalized = Math.max(80, Math.round(width));
            const index = this.table.column(header).index();
            header.style.width = `${normalized}px`;
            this.root.querySelectorAll(`col[data-dt-column~="${index}"]`).forEach((column) => { column.style.width = `${normalized}px`; });
        }

        restoreWidths() { Object.entries(this.widths).forEach(([key, width]) => { const header = this.headerFor(key); if (header) this.setWidth(header, width); }); }

        bindResize() {
            this.root.querySelectorAll('.column-resizer').forEach((handle) => {
                // DataTables sorts on a header click; the drag handle owns its click.
                handle.addEventListener('click', (event) => { event.preventDefault(); event.stopImmediatePropagation(); }, true);
                handle.onpointerdown = (event) => this.resize(event, handle);
            });
        }

        resize(event, handle) {
            const header = handle.closest('th');
            const visible = [...this.grid.tHead.rows[0].cells].filter((cell) => cell.dataset.column && this.table.column(cell).visible());
            const previous = visible[visible.indexOf(header) - 1];
            if (!previous) return;
            event.preventDefault();
            const startX = event.clientX;
            const startWidth = header.getBoundingClientRect().width;
            const previousWidth = previous.getBoundingClientRect().width;
            const move = (pointerEvent) => {
                const width = Math.max(80, Math.min(startWidth + startX - pointerEvent.clientX, startWidth + previousWidth - 80));
                this.setWidth(header, width); this.setWidth(previous, startWidth + previousWidth - width);
            };
            const stop = () => {
                document.removeEventListener('pointermove', move);
                document.body.classList.remove('is-resizing-column');
                this.widths[header.dataset.column] = Math.round(header.getBoundingClientRect().width);
                this.widths[previous.dataset.column] = Math.round(previous.getBoundingClientRect().width);
                localStorage.setItem(this.widthStorageKey, JSON.stringify(this.widths));
            };
            document.body.classList.add('is-resizing-column');
            document.addEventListener('pointermove', move);
            document.addEventListener('pointerup', stop, { once: true });
        }

        bindExport() {
            this.el.exportButton.onclick = () => {
                const indexes = this.table.columns(':visible').indexes().toArray()
                    .filter((index) => !this.config.hasBulkDelete || index !== 0);
                const rows = this.table.rows({ search: 'applied' }).data().toArray();
                const data = [indexes.map((index) => this.$(this.table.column(index).header()).text().trim()), ...rows.map((row) => indexes.map((index) => htmlToText(row[index])))];
                if (window.XLSX) {
                    const book = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(book, XLSX.utils.aoa_to_sheet(data), 'Справочник');
                    XLSX.writeFile(book, `${this.config.id}.xlsx`);
                }
                this.el.actions.hidden = true;
            };
        }

        bindSelection() {
            if (!this.config.hasBulkDelete) return;
            const bulk = this.el.bulk;
            const form = this.root.querySelector('.bulk-delete-form');
            const update = () => {
                const selected = this.grid.querySelectorAll('.row-select:checked').length;
                bulk.hidden = !selected;
                bulk.querySelector('.bulk-delete-count').textContent = selected ? `(${selected})` : '';
            };
            this.root.addEventListener('change', (event) => {
                if (event.target.matches('.select-all')) this.$(this.grid).find('.row-select').prop('checked', event.target.checked);
                if (event.target.matches('.select-all,.row-select')) update();
            });
            bulk.onclick = () => {
                const ids = [...this.grid.querySelectorAll('.row-select:checked')].map((input) => input.value);
                if (!ids.length || !confirm(`Удалить выбранные записи: ${ids.length}?`)) return;
                ids.forEach((id) => form.insertAdjacentHTML('beforeend', `<input name="ids[]" value="${id}">`));
                form.submit();
            };
        }

        bindBackToTop() {
            const button = this.root.querySelector('.table-back-to-top');
            const target = this.root.querySelector('.dt-scroll-body') || window;
            const update = () => { button.hidden = (target === window ? window.scrollY : target.scrollTop) < 240; };
            target.addEventListener('scroll', update, { passive: true });
            button.onclick = () => target.scrollTo({ top: 0, behavior: 'smooth' });
            update();
        }

        bindRemoteModals() {
            const showModal = async (link) => {
                try {
                    const response = await fetch(link.href, { credentials: 'same-origin' });
                    const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                    const modal = page.querySelector('.app-modal:not([hidden])');
                    if (!modal) return;
                    document.querySelectorAll('.app-modal:not([hidden])').forEach((current) => current.remove());
                    document.body.append(modal);
                    modal.querySelectorAll('script:not([src])').forEach((script) => {
                        try { Function(script.textContent)(); } catch (error) { console.error('Не удалось инициализировать окно.', error); }
                    });
                    modal.querySelectorAll('.app-modal__close,.button.secondary').forEach((button) => button.onclick = (closeEvent) => { closeEvent.preventDefault(); modal.remove(); });
                    modal.querySelectorAll('[data-remote-modal]').forEach((nestedLink) => nestedLink.onclick = async (nestedEvent) => {
                        nestedEvent.preventDefault();
                        await showModal(nestedLink);
                    });
                    modal.onclick = (modalEvent) => { if (modalEvent.target === modal) modal.remove(); };
                    window.lucide?.createIcons();
                } catch { /* A failed modal request must not break the catalog. */ }
            };
            this.root.addEventListener('click', async (event) => {
                const link = event.target.closest('.user-card-link,.table-record-link,[data-remote-modal]');
                if (!link) return;
                event.preventDefault();
                await showModal(link);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-catalog-table]').forEach((root) => {
            if (root.dataset.ready) return;
            root.dataset.ready = 'true';
            new CatalogTable(root).init();
        });
    });
})();
</script>
@endverbatim
