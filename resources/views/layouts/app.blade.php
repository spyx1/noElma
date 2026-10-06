<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Отдел тестирования' }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.35.0/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/2.1.2/css/colReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/2.1.2/js/dataTables.colReorder.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js" defer></script>

    <style>
        :root { --app-sidebar-width: 268px; --tblr-primary: #356d98; --tblr-primary-rgb: 53, 109, 152; }
        * { box-sizing: border-box; }
        html, body { width: 100%; min-height: 100%; margin: 0 !important; padding: 0 !important; }
        body { min-width: 320px; background: #f4f6fa; color: #182433; font: 15px/1.5 var(--tblr-font-sans-serif, Inter, system-ui, sans-serif); }
        a, a:hover, a:focus, a:active { text-decoration: none !important; }

        /* App shell and navigation */
        .app-shell { display: grid; width: 100%; min-height: 100vh; margin: 0 !important; padding: 0 !important; grid-template-columns: var(--app-sidebar-width) minmax(0, 1fr); transition: grid-template-columns .2s ease; }
        .app-shell--guest { display: block; }
        .app-shell--guest .app-content { display: flex; align-items: center; min-height: 100vh; }
        .app-shell--guest .app-content-inner { width: 100%; }
        .app-sidebar { position: sticky; top: 0; display: flex; flex-direction: column; height: 100vh; overflow: hidden auto; border-right: 1px solid #e6e7e9; background: #fff; }
        .app-brand { display: flex; align-items: center; gap: 11px; min-height: 76px; padding: 0 20px; border-bottom: 1px solid #e6e7e9; color: #182433; }
        .app-brand-copy { color: #fff; font-size: 23px; font-weight: 800; letter-spacing: -.055em; line-height: 1; }
        .app-brand-no { color: #f3bd5b; }
        .app-brand-elma { color: #fff; }
        .sidebar-toggle { display: grid; place-items: center; width: 32px; height: 32px; margin-left: auto; padding: 0; border: 0; border-radius: 7px; background: transparent; color: #667382; font-size: 20px; }
        .sidebar-toggle:hover { background: #f1f5f9; color: #206bc4; }
        .app-nav { display: grid; gap: 3px; padding: 16px 12px 100px; }
        .app-nav-link { display: flex; align-items: center; gap: 11px; min-height: 42px; padding: 9px 11px; border-radius: 8px; color: #4b5563; font-size: 14px; font-weight: 600; white-space: nowrap; }
        .app-nav-link:hover { background: #f1f5f9; color: #206bc4; }
        .app-nav-link.active { background: #e9f2ff; color: #206bc4; }
        .app-nav-link .ti { width: 20px; font-size: 20px; text-align: center; }
        .nav-count { display: inline-grid; place-items: center; min-width: 20px; height: 20px; margin-left: auto; border-radius: 99px; background: #e9f2ff; color: #206bc4; font-size: 11px; }
        .nav-group { margin: 10px 0 0; }
        .nav-group > summary { display: flex; align-items: center; justify-content: space-between; padding: 8px 11px; cursor: pointer; list-style: none; color: #8091a7; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .nav-group > summary::-webkit-details-marker { display: none; }
        .nav-group > summary .nav-group-chevron { display: grid; place-items: center; width: 16px; height: 16px; font-size: 16px; line-height: 1; transition: transform .2s ease; }
        .nav-group:not([open]) > summary .nav-group-chevron { transform: rotate(-90deg); }
        .nav-group-links { display: grid; gap: 3px; margin-top: 2px; }
        .sidebar-user { position: sticky; bottom: 0; margin-top: auto; padding: 12px; border-top: 1px solid #e6e7e9; background: #fff; }
        .sidebar-user-card { display: flex; align-items: center; gap: 10px; padding: 9px; border-radius: 8px; background: #f8fafc; }
        .sidebar-user-card > .user-avatar { flex: 0 0 36px; width: 36px; height: 36px; }
        .sidebar-user-copy { flex: 1; min-width: 0; }
        .sidebar-user-card form { display: flex; flex: none; margin: 0 0 0 auto; }
        .sidebar-user-name { display: block; overflow: hidden; font-size: 13px; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
        .sidebar-user-role { display: block; color: #8091a7; font-size: 12px; }
        .sidebar-logout { display: grid; place-items: center; width: 30px; height: 30px; margin-left: auto; border: 0; border-radius: 7px; background: transparent; color: #667382; font-size: 18px; }
        .sidebar-logout:hover { background: #fff0f0; color: #d63939; }

        .app-main { min-width: 0; }
        .profile-menu { display: flex; align-items: center; gap: 9px; margin-left: auto; color: #4b5563; font-size: 14px; font-weight: 600; }
        .app-content { padding: 28px; }
        .app-content-inner { width: min(100%, 1600px); margin: 0 auto; }

        /* Generic Tabler-compatible components */
        .card { border: 1px solid #e6e7e9; border-radius: 10px; box-shadow: 0 1px 2px rgba(24,36,51,.04); }
        .btn, button, .button { display: inline-flex; align-items: center; justify-content: center; gap: 5px; min-height: 32px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        button svg, .button svg { width: 15px; height: 15px; }
        :where(button:not(.sidebar-toggle):not(.sidebar-logout):not(.table-filter-button):not(.table-menu-button):not(.clear-active-filter):not(.close-filter), .button) { padding: 5px 10px; border: 1px solid #206bc4; background: #206bc4; color: #fff; }
        :where(button:hover, .button:hover) { background: #1a5aa7; color: #fff; }
        .button.secondary, button.secondary { border-color: #dce1e7; background: #fff; color: #4b5563; }
        .button.secondary:hover, button.secondary:hover { background: #f5f7fa; color: #182433; }
        .danger { border-color: #d63939 !important; background: #d63939 !important; }
        .password-action { border-color: #f59f00 !important; background: #f59f00 !important; }
        .muted, .text-secondary { color: #667382 !important; }
        .form-hint { display: block; margin-top: 5px; font-style: italic; }
        .error { margin: 5px 0 0; color: #d63939; font-size: 13px; }
        .badge { font-weight: 600; }

        /* Catalogs and DataTables */
        .catalog-page { overflow: visible; border: 1px solid #e6e7e9; border-radius: 10px; background: #fff; box-shadow: 0 1px 2px rgba(24,36,51,.04); }
        .catalog-heading { display: flex; align-items: center; gap: 12px; min-height: 70px; margin: 0; padding: 14px 18px; border-bottom: 1px solid #e6e7e9; }
        .catalog-heading h1 { flex: none; margin: 0; font-size: 21px; font-weight: 700; }
        .create-icon-button { margin-left: auto; white-space: nowrap; }
        .data-table { position: relative; padding: 0; }
        .data-table .dt-container > .dt-layout-row,
        .data-table .dt-container > .dt-layout-row.dt-layout-table { margin: 0 !important; }
        .data-table-tools { display: flex; gap: 10px; margin-bottom: 14px; }
        .table-search { display: flex; align-items: center; width: min(100%, 520px); min-height: 40px; padding: 0 10px; border: 1px solid #cdd7e1; border-radius: 7px; background: #fff; color: #667382; }
        .table-search input { flex: 1; min-width: 0; margin: 0; padding: 8px; border: 0; background: transparent; font: inherit; outline: 0; }
        .data-table-count { padding-left: 10px; border-left: 1px solid #e6e7e9; font-size: 13px; white-space: nowrap; }
        .table-filter-button, .table-menu-button { display: grid; place-items: center; width: 38px; height: 38px; padding: 0; border: 1px solid #cdd7e1; border-radius: 7px; background: #fff; color: #206bc4; }
        .table-filter-button:hover, .table-menu-button:hover { background: #e9f2ff; }
        /* Shared searchable user select (Select2). */
        .select2-container { font-size: 13px; font-weight: 400; }
        .select2-container--default .select2-selection--single { height: 40px; border-color: #cbdad5; border-radius: 7px; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { padding-left: 11px; color: #223047; font-weight: 400; line-height: 38px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; right: 7px; }
        .select2-container--default.select2-container--focus .select2-selection--single { border-color: #356d98; }
        .select2-dropdown { border-color: #cbdad5; border-radius: 7px; box-shadow: 0 8px 20px rgba(24,50,75,.12); }
        .select2-search--dropdown { padding: 8px; }
        .select2-container--default .select2-search--dropdown .select2-search__field { min-height: 34px; margin: 0; padding: 6px 9px; border-color: #b8d8d0; border-radius: 6px; font-size: 13px; }
        .select2-results__option { padding: 7px 10px; font-size: 13px; }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background: #356d98; }
        .table-filter-status { display: inline-flex; align-items: center; gap: 6px; max-width: 300px; min-height: 32px; padding: 3px 4px 3px 9px; border: 1px solid #b9d7fa; border-radius: 7px; background: #e9f2ff; color: #206bc4; font-size: 12px; }
        .table-filter-status > svg { flex: none; width: 15px; height: 15px; }
        .table-filter-status > span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .table-filter-status .clear-active-filter { display: grid; flex: none; place-items: center; width: 24px; min-height: 24px; height: 24px; padding: 0; border: 1px solid #a8c9c2; border-radius: 5px; background: #fff; color: #49655f; font-size: 15px; font-weight: 400; line-height: 1; }
        .table-filter-status .clear-active-filter:hover { border-color: #9dbed2; background: #eef5f9; color: #285574; }
        .reference-table-wrap { overflow: auto; border: 1px solid #e6e7e9; border-radius: 8px; }
        .data-table-wrap { overflow: visible; }
        .data-table .data-table-wrap,
        .data-table table.dataTable { border: 0 !important; border-radius: 0 !important; }
        .data-table table.dataTable > thead > tr > th:first-child,
        .data-table table.dataTable > tbody > tr > td:first-child { border-left: 0 !important; }
        .data-table table.dataTable > thead > tr > th:last-child,
        .data-table table.dataTable > tbody > tr > td:last-child { border-right: 0 !important; }
        .data-table table.dataTable > thead > tr:first-child > th { border-top: 0 !important; }
        .data-table table.dataTable { width: 100% !important; margin: 0 !important; border: 0 !important; table-layout: fixed; }
        .data-table table.dataTable thead th { position: sticky; top: 0; z-index: 2; padding: 9px 14px; background: #f8fafc; color: #667382; font-size: 13px; font-weight: 700; letter-spacing: .01em; text-align: left !important; text-transform: none; }
        .data-table table.dataTable thead .dt-column-header { display: flex; align-items: center; width: 100%; }
        .data-table table.dataTable thead .dt-column-order { order: 2; margin-left: auto; }
        .data-table table.dataTable thead th { position: sticky; top: 0; }
        .column-resizer { position: absolute; z-index: 4; top: 0; left: -4px; width: 8px; height: 100%; cursor: col-resize; }
        .column-resizer::after { position: absolute; top: 50%; left: 3px; width: 1px; height: 18px; background: #a8c9c2; content: ''; opacity: 0; transform: translateY(-50%); transition: opacity .15s ease; }
        th:hover .column-resizer::after, body.is-resizing-column .column-resizer::after { opacity: 1; }
        body.is-resizing-column { cursor: col-resize; user-select: none; }
        .data-table table.dataTable tbody td { padding: 12px 14px; border-color: #e6e7e9; color: #182433; font-size: 14px; vertical-align: middle; }
        .data-table table.dataTable tbody tr:hover td { background: #f8fbff; }
        /* Fixed checkbox service column for all shared catalog tables */
        .data-table table.dataTable:has(
            thead th:first-child input[type="checkbox"]
        ) colgroup col:first-child {
            width: 44px !important;
            min-width: 44px !important;
            max-width: 44px !important;
        }
        .data-table table.dataTable:has(
            thead th:first-child input[type="checkbox"]
        ) thead th:first-child,
        .data-table table.dataTable:has(
            thead th:first-child input[type="checkbox"]
        ) tbody td:first-child {
            width: 44px !important;
            min-width: 44px !important;
            max-width: 44px !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            box-sizing: border-box;
            text-align: center !important;
        }
        .data-table table.dataTable:has(
            thead th:first-child input[type="checkbox"]
        ) thead th:first-child .dt-column-header {
            justify-content: center !important;
        }
        .data-table table.dataTable:has(
            thead th:first-child input[type="checkbox"]
        ) thead th:first-child .dt-column-order {
            display: none !important;
        }
        .data-table table.dataTable:has(
            thead th:first-child input[type="checkbox"]
        ) thead th:first-child input[type="checkbox"],
        .data-table table.dataTable:has(
            thead th:first-child input[type="checkbox"]
        ) tbody td:first-child input[type="checkbox"] {
            display: block;
            width: 16px;
            height: 16px;
            margin: 0 auto !important;
        }
        .data-table input[type="checkbox"] { width: 16px; height: 16px; margin: 0; accent-color: #206bc4; }
        .data-table .user-cell { display: inline-flex; align-items: center; gap: 8px; color: #356d98; font-weight: 400; }
        .user-avatar { display: inline-flex; flex: none; align-items: center; justify-content: center; overflow: hidden; border-radius: 50%; background: var(--avatar-color, #2f80ed); color: #fff; font-size: 10px; font-weight: 700; line-height: 1; object-fit: cover; }
        img.user-avatar { display: block; }
        .data-table-loader { display: none; }
        .data-table.is-initializing .data-table-tools { display: none !important; }
        .data-table.is-initializing .data-table-loader { display: flex; align-items: center; justify-content: center; min-height: 220px; gap: 12px; border: 1px solid #dce6e3; border-radius: 8px; background: linear-gradient(135deg, #fbfdfc 0%, #f2f8f6 100%); color: #4e6778; opacity: 0; animation: reveal-table-loader .16s ease-out .25s forwards; }
        .data-table.is-initializing .reference-table-wrap { position: absolute; inset: 0; display: block; width: 100%; visibility: hidden; pointer-events: none; }
        .data-table-spinner { width: 28px; height: 28px; border: 3px solid #c9dfec; border-top-color: #356d98; border-radius: 50%; animation: spin .7s linear infinite; }
        .data-table-loader-copy { display: grid; gap: 1px; }
        .data-table-loader-copy strong { color: #29485b; font-size: 13px; font-weight: 700; }
        .data-table-loader-copy small { color: #748894; font-size: 12px; }
        @keyframes reveal-table-loader { to { opacity: 1; } }
        .table-back-to-top { position: fixed; z-index: 30; right: 22px; bottom: 22px; display: grid; place-items: center; width: 38px; min-height: 38px; height: 38px; padding: 0; border: 1px solid #b7cfdf; border-radius: 50%; background: #fff; color: #356d98; box-shadow: 0 6px 16px rgba(24,50,75,.16); }
        .table-back-to-top:hover { border-color: #87afc8; background: #eef5f9; color: #285574; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Popovers and dialogs */
        .table-actions-menu, .table-settings-panel { position: absolute; z-index: 20; padding: 8px; border: 1px solid #e6e7e9; border-radius: 8px; background: #fff; box-shadow: 0 8px 24px rgba(24,36,51,.12); }
        .table-actions-menu button { display: flex; width: 100%; justify-content: flex-start; border: 0; background: transparent; color: #182433; }
        .table-actions-menu button:hover { background: #f1f5f9; color: #206bc4; }
        .table-settings-panel { width: 260px; }
        .column-settings-list { margin: 8px 0; padding: 0; list-style: none; }
        .column-settings-list li { display: flex; align-items: center; gap: 8px; padding: 6px 2px; }
        .table-filter-overlay, .modal { position: fixed; z-index: 1050; inset: 0; display: grid; place-items: center; padding: 20px; background: rgba(24,36,51,.45); }
        .table-filter-overlay[hidden], .modal[hidden] { display: none !important; }
        .table-filter-panel { width: min(100%, 580px); }
        .modal .confirm-card { width: min(100%, 420px); }
        .filter-title, .modal-header { display: flex; align-items: center; justify-content: space-between; padding-bottom: 14px; border-bottom: 1px solid #e6e7e9; }
        .filter-title strong, .modal-header h1 { margin: 0; font-size: 20px; }
        .filter-fields { display: grid; gap: 12px; padding: 16px 0; }
        label { display: block; color: #4b5563; font-size: 14px; font-weight: 600; }
        input:not([type="checkbox"]), select, textarea { width: 100%; margin-top: 6px; padding: 9px 11px; border: 1px solid #cdd7e1; border-radius: 7px; background: #fff; color: #182433; font: inherit; }
        input:focus, select:focus, textarea:focus { outline: 2px solid rgba(32,107,196,.2); border-color: #206bc4; }
        .filter-actions, .form-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-top: 18px; }

        /* Legacy table editor, retained functionally but visually aligned */
        .table-editor-modal { align-items: start; overflow: auto; }
        .modal .table-editor-card { width: min(calc(100vw - 40px), 1320px); max-width: none; margin: 20px auto; }
        .editor-heading { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .editor-details { display: grid; gap: 12px; }
        .editor-details label { display: grid; grid-template-columns: 170px minmax(0,1fr); gap: 14px; align-items: center; }
        .editor-details label:nth-child(2) { grid-template-columns: 170px 205px; }
        .additional-work-type-editor-details .additional-work-type-checkbox-row { grid-template-columns: 178px auto !important; justify-content: start; }
        .additional-work-type-editor-details .additional-work-type-checkbox-row input[type="checkbox"] { width: 16px; height: 16px; margin: 0; justify-self: start; align-self: center; }
        .editor-details input, .editor-details select { margin: 0; }
        .tabs { display: flex; gap: 4px; margin: 12px 0 8px; border-bottom: 1px solid #e6e7e9; }
        .tabs a, .editor-tabs button { padding: 9px 13px; border: 0; border-bottom: 2px solid transparent; border-radius: 0; background: transparent; color: #667382; font-size: 13px; font-weight: 600; }
        .tabs a.active, .editor-tabs button.active { border-bottom-color: #206bc4; color: #206bc4; }
        .editor-section-head { display: flex; align-items: center; justify-content: space-between; margin: 16px 0 10px; }
        .app-info-note { display: flex; align-items: flex-start; gap: 6px; margin-top: 5px; color: #6b7f8e; font-size: 12px; font-weight: 400; line-height: 1.4; }
        .app-info-note > .ti { flex: none; margin-top: 1px; color: #356d98; font-size: 15px; line-height: 1.2; }
        .app-info-note__content { min-width: 0; }
        .reference-table { width: 100%; min-width: 760px; margin: 0; border-collapse: collapse; }
        .reference-table th, .reference-table td { padding: 7px 10px; border: 1px solid #e6e7e9; text-align: left; font-size: 13px; line-height: 1.35; }
        .reference-table th { background: #f8fafc; color: #667382; font-size: 12px; font-weight: 700; letter-spacing: 0; text-transform: none; }
        .editor-table, .column-editor { min-width: 900px; }
        .column-editor input, .column-editor select { margin: 0; border: 0; }
        .check-cell, .drag-handle { text-align: center !important; }
        .icon-button { width: 30px; min-height: 30px; height: 30px; padding: 0; border: 1px solid #cdd7e1 !important; background: #fff !important; color: #206bc4 !important; }
        .table-meta { display: grid; grid-template-columns: 180px 1fr; gap: 10px 18px; margin: 20px 0; }
        .table-meta dd { margin: 0; }
        .service-info { margin-top: 20px; border-top: 1px solid #e6e7e9; }
        .service-info summary { padding: 12px 0; color: #206bc4; cursor: pointer; font-weight: 600; }
        .toast { position: fixed; z-index: 1100; top: 20px; right: 20px; display: flex; align-items: center; gap: 8px; min-width: 300px; padding: 13px 16px; border: 1px solid #b9dfca; border-radius: 8px; background: #ecf9f0; color: #176b3a; box-shadow: 0 8px 24px rgba(24,36,51,.12); }
        .toast.is-hidden { opacity: 0; pointer-events: none; transform: translateY(-8px); }

        /* Northern sea theme: navy, teal, coral and amber */
        body { background: #f5f7f4; color: #223047; }
        a { color: #356d98; }
        .app-sidebar { border-color: #203c56; background: linear-gradient(180deg, #18324b 0%, #11263b 100%); color: #eaf4f5; }
        .app-brand, .sidebar-user { border-color: rgba(202, 224, 226, .16); background: transparent; color: #fff; }
        .app-brand-kicker, .sidebar-user-role, .nav-group > summary { color: #9fc5c7; }
        .sidebar-toggle, .sidebar-logout { color: #b9dcdb !important; }
        .sidebar-toggle:hover, .sidebar-logout:hover { background: rgba(255,255,255,.12); color: #f3bd5b !important; }
        .app-nav-link { color: #d9eced; }
        .app-nav-link:hover { background: rgba(63, 180, 170, .16); color: #fff; }
        .app-nav-link.active { background: #0f766e; color: #fff; box-shadow: inset 3px 0 #f3bd5b; }
        .nav-count { background: #f3bd5b; color: #18324b; }
        .sidebar-user-card { background: rgba(255,255,255,.08); }
        .sidebar-user-name { color: #fff; }
        .app-topbar, .catalog-page { border-color: #dce6e3; }
        .profile-menu, label { color: #334155; }
        .muted, .text-secondary { color: #667a89 !important; }
        .card, .catalog-page, .reference-table-wrap, .table-actions-menu, .table-settings-panel, .filter-title, .modal-header, .tabs, .reference-table th, .reference-table td, .service-info { border-color: #dce6e3; }
        :where(button:not(.sidebar-toggle):not(.sidebar-logout):not(.table-filter-button):not(.table-menu-button):not(.clear-active-filter):not(.close-filter), .button) { border-color: #0f766e; background: #0f766e; }
        :where(button:hover, .button:hover) { background: #0b5d57; }
        .create-icon-button { border-color: #b8dcd3 !important; background: #e4f3ef !important; color: #0f766e !important; }
        .create-icon-button:hover { border-color: #9bcfc2 !important; background: #d2ebe4 !important; color: #0b5d57 !important; }
        .password-action { border-color: #d99828 !important; background: #d99828 !important; }
        .table-search, .table-filter-button, .table-menu-button, input:not([type="checkbox"]), select, textarea, .icon-button { border-color: #c7d8e4 !important; }
        .table-filter-button, .table-menu-button, .table-filter-status, .data-table .user-cell, .icon-button, .service-info summary { color: #356d98 !important; }
        .table-filter-button:hover, .table-menu-button:hover, .table-actions-menu button:hover { background: #eef5f9; color: #285574; }
        .table-filter-status { border-color: #c4d9e6; background: #f5f9fc; }
        .data-table-count { border-color: #dce6e3; }
        .data-table table.dataTable thead th, .reference-table th { background: #eef5f9; color: #4d687c; }
        .data-table table.dataTable tbody td { border-color: #dce6e3; color: #223047; }
        .data-table table.dataTable tbody tr:hover td { background: #fffaf0; }
        .data-table table.dataTable tbody a,
        .data-table .reference-table a,
        .data-table .user-cell {
            color: #356d98 !important;
        }
        .data-table table.dataTable tbody a:hover,
        .data-table .reference-table a:hover,
        .data-table a.user-cell:hover {
            color: #285574 !important;
        }
        .data-table input[type="checkbox"] { accent-color: #356d98; }
        .data-table-spinner { border-color: #c9dfec; border-top-color: #356d98; }
        input:focus, select:focus, textarea:focus { outline-color: rgba(53,109,152,.2); border-color: #356d98; }
        .tabs a.active, .editor-tabs button.active { border-bottom-color: #356d98; color: #356d98; }

        /* Shared catalog toolbar */
        .catalog-heading { min-height: 62px; gap: 12px; padding: 10px 14px; }
        .catalog-heading h1 { font-size: 19px; line-height: 1.2; }
        .catalog-heading .table-search { flex: 0 1 480px; width: auto; min-height: 36px; height: 36px; margin: 0; padding: 0 0 0 8px; overflow: hidden; border-radius: 7px; }
        .table-search > svg { flex: none; width: 17px; height: 17px; }
        .table-search input { display: block; align-self: center; height: 34px; min-height: 34px; margin: 0 !important; padding: 0 7px !important; border: 0 !important; appearance: none; -webkit-appearance: none; box-shadow: none !important; color: #223047; font-size: 13px; line-height: normal; outline: 0 !important; transform: none; }
        .table-search input::placeholder { color: #7b8b96; opacity: 1; }
        .data-table-count { display: flex; align-items: center; align-self: stretch; padding: 0 8px; border-left: 1px solid #dce6e3; color: #5b7080; font-size: 11px; font-weight: 600; }
        .table-search .table-filter-button { align-self: center; width: 30px; min-height: 30px; height: 30px; margin-right: 2px; border: 0 !important; border-left: 1px solid #dce6e3 !important; border-radius: 0; }
        .table-menu-button { flex: none; width: 30px; min-height: 30px; height: 30px; border-radius: 6px; }
        .table-filter-button svg, .table-menu-button svg { width: 15px; height: 15px; }
        .create-icon-button { min-height: 32px; padding: 5px 9px !important; font-size: 12px; }
        .catalog-heading .table-menu { display: flex; margin-left: 0; }
        .catalog-heading .table-menu-button { min-height: 30px; }
        .table-menu { position: relative; }
        .table-menu .table-actions-menu,
        .table-menu .table-settings-panel { top: calc(100% + 8px); right: 0; left: auto; }
        .table-menu .table-actions-menu { width: 224px; padding: 6px; }
        .table-menu .table-settings-panel { width: 300px; padding: 0; overflow: hidden; border-radius: 10px; }
        .table-menu .table-actions-menu button { width: 100%; min-height: 32px; padding: 6px 9px !important; border: 0 !important; border-radius: 6px; background: transparent !important; color: #334155 !important; font-size: 12px; font-weight: 600; justify-content: flex-start; white-space: nowrap; }
        .table-menu .table-actions-menu button:hover { background: #eef5f9 !important; color: #285574 !important; }
        .table-menu .table-actions-menu button svg { flex: none; width: 17px; height: 17px; }
        .table-settings-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 14px 14px 12px; border-bottom: 1px solid #dce6e3; }
        .table-settings-header strong { display: block; color: #223047; font-size: 14px; line-height: 1.3; }
        .table-settings-header span { display: block; margin-top: 3px; color: #667a89; font-size: 11px; line-height: 1.35; }
        .table-settings-header .close-table-settings { display: grid; flex: none; place-items: center; width: 28px; min-height: 28px; height: 28px; padding: 0 !important; border: 0 !important; border-radius: 6px; background: transparent !important; color: #667a89 !important; }
        .table-settings-header .close-table-settings:hover { background: #edf4f2 !important; color: #223047 !important; }
        .table-settings-header .close-table-settings svg { width: 16px; height: 16px; }
        .table-menu .column-settings-list { display: grid; gap: 4px; margin: 0; padding: 10px; }
        .table-menu .column-settings-list li { min-height: 36px; padding: 6px 8px; border: 1px solid transparent; border-radius: 7px; background: #f8fbfa; color: #334155; font-size: 13px; }
        .table-menu .column-settings-list li:hover { border-color: #c5ddd7; background: #f1f8f6; }
        .table-menu .column-settings-list li label { display: flex; flex: 1; align-items: center; gap: 8px; margin: 0; cursor: pointer; font-size: 13px; font-weight: 600; }
        .table-menu .column-settings-list li input { flex: none; width: 15px; height: 15px; margin: 0; accent-color: #356d98; }
        .table-menu .column-order-handle { flex: none; width: 16px; height: 16px; color: #7b8f99; cursor: grab; }
        .table-menu .column-settings-list li.is-drop-before { border-top-color: #356d98; }
        .table-menu .column-settings-list li.is-drop-after { border-bottom-color: #356d98; }
        .table-settings-footer { padding: 10px; border-top: 1px solid #dce6e3; background: #f8fbfa; }
        .table-menu .reset-columns { width: 100%; min-height: 32px; padding: 5px 9px !important; border: 1px solid #cbdad5 !important; background: #fff !important; color: #405467 !important; font-size: 12px; }
        .table-menu .reset-columns:hover { background: #edf4f2 !important; color: #223047 !important; }

        /* Filter dialog follows the same visual system as other dialogs */
        .table-filter-panel { width: min(100%, 580px); padding: 0; overflow: hidden; }
        .filter-title { min-height: 44px; padding: 7px 14px; }
        .filter-title strong { color: #223047; font-size: 14px; line-height: 1.2; }
        .filter-title .close-filter { width: 26px; height: 26px; font-size: 18px; }
        .filter-fields { gap: 12px; padding: 20px 24px; }
        .filter-fields label { display: grid; grid-template-columns: 145px minmax(0, 1fr); align-items: center; gap: 14px; color: #405467; font-size: 13px; }
        .filter-fields input, .filter-fields select { min-height: 40px; margin: 0; padding: 8px 11px; font-size: 14px; }
        .filter-actions { justify-content: flex-end; margin: 0; padding: 16px 24px 20px; border-top: 1px solid #dce6e3; background: #f8fbfa; }
        .filter-actions .apply-filter { border-color: #0f766e; background: #0f766e; color: #fff; }
        .filter-actions .apply-filter:hover { background: #0b5d57; }
        .filter-actions .reset-filter { border-color: #e2b44f; background: #fff7df; color: #805b0d; }
        .filter-actions .reset-filter:hover { background: #f8e7b9; color: #684806; }
        .filter-actions .cancel-filter { border-color: #cbdad5; background: #fff; color: #405467; }
        .filter-actions .cancel-filter:hover { background: #edf4f2; color: #223047; }
        .table-bulk-delete { border-color: #d63939 !important; background: #d63939 !important; color: #fff !important; }

        /* Shared standard dialogs and forms */
        /* Shared modal component */
        .app-modal { padding: 20px; }
        .app-modal__dialog { display: flex; flex-direction: column; width: min(100%, 580px); max-height: calc(100vh - 40px); margin: 0; padding: 0; overflow: hidden; border: 0; border-radius: 10px; background: #fff; box-shadow: 0 1rem 3rem rgba(24,36,51,.22); }
        .app-modal__header { display: flex; flex: none; align-items: center; justify-content: space-between; min-height: 50px; padding: 12px 18px; border-bottom: 1px solid #dce6e3; }
        .app-modal__header h1, .app-modal__header h2 { margin: 0; color: #223047; font-size: 16px; font-weight: 700; line-height: 1.25; }
        .app-modal__header-actions { display: inline-flex; flex: none; align-items: center; gap: 2px; }
        .app-modal__utility, .app-modal__close { display: grid; flex: none; place-items: center; width: 26px; min-height: 26px; height: 26px; padding: 0 !important; border: 0 !important; border-radius: 6px; background: transparent !important; color: #667a89 !important; }
        .app-modal__utility:hover, .app-modal__utility[aria-pressed="true"], .app-modal__close:hover { background: #edf4f2 !important; color: #223047 !important; }
        .app-modal__utility svg, .app-modal__close svg { width: 16px; height: 16px; }
        .app-modal__service { flex: none; padding: 12px 18px; border-bottom: 1px solid #dce6e3; background: #f8fbfa; }
        .app-modal__service .table-meta { grid-template-columns: 150px minmax(0, 1fr); gap: 7px 14px; margin: 0; font-size: 13px; }
        .app-modal__service .table-meta dt { color: #667a89; font-weight: 600; }
        .app-modal__service .user-cell { font-weight: 400; }
        .app-modal__body { min-height: 0; padding: 18px; overflow: auto; }
        .app-modal__body form { display: grid; gap: 12px; margin: 0; }
        .app-modal__body label, .app-modal__body #password-fields { display: grid; gap: 6px; margin: 0; color: #405467; font-size: 13px; font-weight: 700; }
        .app-modal__body input:not([type="checkbox"]), .app-modal__body select, .app-modal__body textarea { min-height: 40px; margin: 0; padding: 8px 11px; border-color: #cbdad5; border-radius: 7px; font-family: inherit; font-size: 13px; font-weight: 400; }
        .app-modal__body input:focus, .app-modal__body select:focus, .app-modal__body textarea:focus { outline: 2px solid rgba(53,109,152,.16); border-color: #356d98; }
        .app-modal__footer { display: flex; flex: none; align-items: center; justify-content: flex-start; gap: 8px; min-height: 52px; padding: 10px 18px; border-top: 1px solid #dce6e3; background: #f8fbfa; }
        .app-modal__footer > .button:not(.secondary), .app-modal__footer > button:not(.secondary) { color: #fff; }
        .app-modal__footer .form-actions { margin: 0; }
        .app-modal--confirm .app-modal__dialog { width: min(100%, 400px); }
        .app-modal--confirm .app-modal__header { min-height: 44px; }
        .app-modal--editor { align-items: flex-start; overflow: auto; }
        .app-modal.app-modal--editor > .app-modal__dialog { width: min(calc(100vw - 40px), 1320px); max-width: none; max-height: none; margin: 20px auto; overflow: visible; }
        .app-modal__dialog.app-modal__dialog--expanded { position: fixed; inset: 0; width: auto !important; max-width: none; max-height: none; min-height: 100vh; margin: 0 !important; border-radius: 0; overflow: auto !important; }
        .table-workspace { display: grid; gap: 16px; }
        .table-workspace form { gap: 14px; }
        .table-workspace .table-meta { margin: 0; }
        .table-workspace > .table-meta { grid-template-columns: auto auto; justify-content: start; gap: 4px 12px; color: #405467; font-size: 13px; }
        .table-workspace > .table-meta dt { font-weight: 600; }
        .table-workspace .editor-details { gap: 10px; }
        .table-workspace .tabs { margin: 0; }
        .table-workspace .editor-section-head { align-items: flex-start; margin: 0 0 10px; }
        .table-workspace .editor-section-head h2 { margin: 0; color: #334155; font-size: 18px; line-height: 1.25; }
        .table-workspace .reference-table-wrap, .table-workspace .service-info { margin: 0; }
        .table-workspace .reference-table th, .table-workspace .reference-table td { padding: 5px 8px; }
        .table-workspace .reference-table th svg { width: 16px; height: 16px; stroke-width: 2; }
        .table-workspace .drag-handle { cursor: grab; }
        .table-workspace .drag-handle:active, .table-workspace .column-row.is-dragging .drag-handle { cursor: grabbing; }
        .table-workspace .drag-handle svg { width: 14px; height: 14px; }
        .table-workspace .column-editor input:not([type="checkbox"]), .table-workspace .column-editor select { min-height: 30px; padding: 4px 7px; font-size: 13px; }
        .table-workspace .column-editor th, .table-workspace .column-editor th:last-child, .table-workspace .column-editor td:last-child { text-align: center; }
        .table-workspace .column-editor .icon-button { width: 26px; min-height: 26px; height: 26px; }
        .table-workspace .reference-table input[type="checkbox"] { appearance: none; display: inline-grid; box-sizing: border-box; width: 14px; min-width: 14px; max-width: 14px; height: 14px; min-height: 14px; max-height: 14px; margin: 0; padding: 0; place-content: center; border: 1px solid #a9bbc7; border-radius: 3px; background: #fff; vertical-align: middle; cursor: pointer; }
        .table-workspace .reference-table input[type="checkbox"]::after { width: 7px; height: 4px; border: solid #fff; border-width: 0 0 1.5px 1.5px; content: ''; transform: rotate(-45deg) scale(0); transform-origin: center; }
        .table-workspace .reference-table input[type="checkbox"]:checked { border-color: #0f766e; background: #0f766e; }
        .table-workspace .reference-table input[type="checkbox"]:checked::after { transform: rotate(-45deg) scale(1); }
        .table-workspace .reference-table input[type="checkbox"]:disabled { cursor: default; opacity: .58; }
        .table-filter-panel .app-modal__header { min-height: 44px; padding: 7px 14px; }
        .table-filter-panel .app-modal__header h2 { font-size: 14px; }
        .table-filter-panel .app-modal__body { padding: 0; }
        .table-filter-panel .app-modal__footer { padding: 0; }
        .app-modal__footer .filter-actions { width: 100%; margin: 0; padding: 8px 14px; border: 0; background: transparent; }

        /* Dashboard */
        .workspace-dashboard { display: grid; gap: 18px; }
        .dashboard-topline { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; }
        .dashboard-eyebrow { margin: 0 0 3px; color: #718394; font-size: 12px; font-weight: 600; }
        .dashboard-title { margin: 0; color: #223047; font-size: 24px; line-height: 1.2; }
        .dashboard-subtitle { margin: 5px 0 0; color: #667a89; font-size: 13px; }
        .dashboard-role-switch { display: inline-flex; flex-wrap: wrap; gap: 3px; padding: 3px; border: 1px solid #d5e1e8; border-radius: 8px; background: #f8fbfc; }
        .dashboard-role-switch a { padding: 7px 11px; border-radius: 5px; color: #587086; font-size: 13px; font-weight: 600; text-decoration: none; }
        .dashboard-role-switch a:hover { background: #eaf2f7; color: #285574; }
        .dashboard-role-switch a.active { background: #fff; color: #285574; box-shadow: 0 1px 3px rgba(24,50,75,.13); }
        .dashboard-metrics { display: grid; grid-template-columns: minmax(0, 1fr); }
        .dashboard-metric { display: grid; grid-template-columns: 38px 1fr; gap: 12px; align-items: center; min-height: 108px; padding: 17px; border: 1px solid #dce6e3; border-radius: 10px; background: #fff; }
        .dashboard-metric-icon { display: grid; place-items: center; width: 38px; height: 38px; border-radius: 10px; background: #eaf3f8; color: #356d98; }
        .dashboard-metric-icon .ti { font-size: 19px; }
        .dashboard-metric-label { color: #718394; font-size: 12px; }
        .dashboard-metric-value { margin-top: 2px; color: #223047; font-size: 26px; font-weight: 700; line-height: 1; }
        .dashboard-metric-note { margin-top: 5px; color: #82929f; font-size: 12px; }
        .dashboard-chief-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .dashboard-chief-metrics .dashboard-metric { min-height: 94px; padding: 14px; }
        .dashboard-chief-metrics .dashboard-metric-value { font-size: 23px; }
        .dashboard-staff-table { width: 100%; border-collapse: collapse; }
        .dashboard-staff-table th, .dashboard-staff-table td { padding: 10px 8px; border-top: 1px solid #e5edf0; color: #42596b; font-size: 12px; text-align: left; }
        .dashboard-staff-table th { padding-top: 0; border-top: 0; color: #8092a0; font-size: 11px; font-weight: 700; }
        .dashboard-staff-table th:first-child, .dashboard-staff-table td:first-child { width: 34%; }
        .dashboard-staff-table th:not(:first-child), .dashboard-staff-table td:not(:first-child) { text-align: center; }
        .dashboard-staff-table td:last-child, .dashboard-staff-table th:last-child { text-align: right; }
        .dashboard-staff-person { display: inline-flex; align-items: center; gap: 7px; color: #356d98; font-weight: 600; }
        .dashboard-staff-avatar { display: grid; flex: none; place-items: center; width: 24px; height: 24px; border-radius: 50%; background: #eaf3f8; color: #356d98; font-size: 10px; font-weight: 700; }
        .dashboard-staff-state { display: inline-block; color: #0f766e; font-size: 11px; white-space: nowrap; }
        .dashboard-staff-state::before { display: inline-block; width: 6px; height: 6px; margin-right: 4px; border-radius: 50%; background: currentColor; content: ''; }
        .dashboard-staff-state.rest { color: #8596a3; }
        .dashboard-grid > .dashboard-shift-summary { align-self: start; grid-template-columns: 1fr; }
        .dashboard-grid > .dashboard-shift-summary .dashboard-shift-times { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .dashboard-shift-summary { display: grid; grid-template-columns: auto minmax(0, 1fr); gap: 14px; align-items: center; min-height: 108px; padding: 15px 17px; border: 1px solid #dce6e3; border-radius: 10px; background: #fff; }
        .dashboard-shift-summary h2 { margin: 0; color: #334b5e; font-size: 14px; }
        .dashboard-shift-summary p { margin: 4px 0 0; color: #7b8e9d; font-size: 12px; }
        .dashboard-shift-times { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
        .dashboard-shift-time { padding-left: 10px; border-left: 2px solid #dce8ed; }
        .dashboard-shift-time:first-child { border-left-color: #356d98; }
        .dashboard-shift-time:nth-child(2) { border-left-color: #7cc7b9; }
        .dashboard-shift-time:last-child { border-left-color: #d8a950; }
        .dashboard-shift-time span { display: block; color: #7b8e9d; font-size: 11px; }
        .dashboard-shift-time strong { display: block; margin-top: 3px; color: #2b4356; font-size: 18px; line-height: 1; }
        .dashboard-pending-work { margin-top: 7px; color: #718394; font-size: 12px; }
        .dashboard-pending-work strong { color: #a36312; }
        .dashboard-shift-progress { grid-column: 1 / -1; display: grid; gap: 5px; }
        .dashboard-shift-progress-copy { display: flex; justify-content: space-between; color: #718394; font-size: 11px; }
        .dashboard-shift-progress-copy strong { color: #356d98; font-size: 12px; }
        .dashboard-shift-progress-bar { display: block; height: 7px; overflow: hidden; border-radius: 999px; background: #e3edf1; }
        .dashboard-shift-progress-bar span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #0f766e, #57b89f); transition: width .2s ease; }
        .dashboard-shift-panel { padding: 17px; border: 1px solid #dce6e3; border-radius: 10px; background: #fff; }
        .dashboard-shift-panel .dashboard-panel-head { align-items: center; }
        .dashboard-shift-panel .dashboard-panel-head p { white-space: nowrap; }
        .dashboard-shift-calendar { display: grid; grid-template-columns: repeat(14, minmax(54px, 1fr)); gap: 7px; overflow-x: auto; }
        .dashboard-shift-day { min-width: 54px; padding: 8px 7px; border: 1px solid #dfe9ed; border-radius: 7px; background: #f9fbfc; color: inherit; font: inherit; text-align: center; text-decoration: none; cursor: pointer; }
        .dashboard-shift-day:hover { border-color: #8fb8ce; background: #f1f7fa; }
        .dashboard-shift-day time { display: block; color: #6d8190; font-size: 11px; }
        .dashboard-shift-day strong { display: block; margin-top: 6px; color: #8597a3; font-size: 12px; font-weight: 600; }
        .dashboard-shift-day.is-working { border-color: #b9d7e6; background: #eef6fa; }
        .dashboard-shift-day.is-working strong { color: #356d98; }
        .dashboard-shift-day.is-selected { box-shadow: inset 0 0 0 1px #0f766e; }
        .dashboard-shift-day.is-selected time { color: #0f766e; font-weight: 700; }
        .dashboard-grid { display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(320px, .7fr); gap: 14px; }
        .dashboard-grid--single { grid-template-columns: minmax(0, 1fr); }
        .dashboard-chart-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .dashboard-panel { padding: 17px; border: 1px solid #dce6e3; border-radius: 10px; background: #fff; }
        .dashboard-panel-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
        .dashboard-panel-actions { display: inline-flex; align-items: center; gap: 10px; white-space: nowrap; }
        .dashboard-panel-actions .button { min-height: 30px; padding: 5px 9px; color: #fff; font-size: 12px; }
        .dashboard-panel h2 { margin: 0; color: #2d4052; font-size: 16px; line-height: 1.25; }
        .dashboard-panel-head p { margin: 4px 0 0; color: #82929f; font-size: 12px; }
        .dashboard-legend { display: inline-flex; flex-wrap: wrap; gap: 12px; color: #718394; font-size: 12px; }
        .dashboard-legend i { display: inline-block; width: 8px; height: 8px; margin-right: 4px; border-radius: 50%; background: #356d98; }
        .dashboard-legend i.secondary { background: #7cc7b9; }
        .dashboard-chart { width: 100%; height: 206px; }
        .dashboard-chart text { fill: #8191a0; font-family: inherit; font-size: 11px; }
        .dashboard-chart .grid-line { stroke: #e7eef1; stroke-width: 1; }
        .dashboard-chart .area { fill: rgba(53,109,152,.10); }
        .dashboard-chart .line { fill: none; stroke: #356d98; stroke-linecap: round; stroke-linejoin: round; stroke-width: 3; }
        .dashboard-chart .point { fill: #fff; stroke: #356d98; stroke-width: 3; }
        .dashboard-chart .secondary-line { fill: none; stroke: #7cc7b9; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2; stroke-dasharray: 5 5; }
        .dashboard-chart .bar-norm { fill: #aabcf0; }
        .dashboard-chart .bar-fact { fill: #58c69e; }
        .dashboard-task-list, .dashboard-test-list { display: grid; gap: 0; }
        .dashboard-task, .dashboard-test { display: grid; grid-template-columns: auto minmax(0, 1fr) auto; gap: 10px; align-items: center; padding: 11px 0; border-top: 1px solid #e8eef0; }
        .dashboard-task:first-child, .dashboard-test:first-child { padding-top: 0; border-top: 0; }
        .dashboard-task:last-child, .dashboard-test:last-child { padding-bottom: 0; }
        .dashboard-priority { width: 8px; height: 8px; border-radius: 50%; background: #e28a28; }
        .dashboard-priority.overdue { background: #d84b35; }
        .dashboard-task a, .dashboard-test a { color: #356d98; font-size: 13px; font-weight: 600; line-height: 1.35; text-decoration: none; }
        .dashboard-task small, .dashboard-test small { display: block; margin-top: 3px; color: #8191a0; font-size: 11px; }
        .dashboard-due { color: #697c8b; font-size: 11px; text-align: right; white-space: nowrap; }
        .dashboard-status { display: inline-block; min-width: 70px; padding: 4px 7px; border-radius: 999px; background: #edf5f7; color: #356d98; font-size: 11px; font-weight: 600; text-align: center; white-space: nowrap; }
        .dashboard-status.is-progress { background: #e6f3ef; color: #0f766e; }
        .dashboard-progress { display: grid; gap: 5px; min-width: 82px; color: #667a89; font-size: 11px; text-align: right; }
        .dashboard-progress-bar { width: 82px; height: 5px; overflow: hidden; border-radius: 999px; background: #e6edf1; }
        .dashboard-progress-bar span { display: block; height: 100%; border-radius: inherit; background: #0f766e; }
        .dashboard-empty { display: grid; place-items: center; min-height: 260px; padding: 20px; border: 1px dashed #cbdce4; border-radius: 10px; background: #fbfcfd; color: #718394; text-align: center; }
        .dashboard-empty .ti { display: block; margin-bottom: 8px; color: #89a3b5; font-size: 28px; }
        .dashboard-updates { padding: 17px; border: 1px solid #dce6e3; border-radius: 10px; background: #fff; }
        .dashboard-update-list { display: grid; }
        .dashboard-update { display: grid; grid-template-columns: 88px 30px minmax(0, 1fr); gap: 10px; align-items: start; padding: 12px 0; border-top: 1px solid #e4ecef; }
        .dashboard-update:first-child { padding-top: 0; border-top: 0; }
        .dashboard-update:last-child { padding-bottom: 0; }
        .dashboard-update-icon { display: grid; place-items: center; width: 30px; height: 30px; border-radius: 8px; background: #eaf3f8; color: #356d98; }
        .dashboard-update-title { color: #334b5e; font-size: 13px; font-weight: 700; }
        .dashboard-update-text { margin-top: 3px; color: #718394; font-size: 12px; line-height: 1.4; }
        .dashboard-update-date { padding-top: 8px; color: #8191a0; font-size: 11px; font-style: normal; white-space: nowrap; }

        .sidebar-collapsed { grid-template-columns: 70px minmax(0,1fr); }
        .sidebar-collapsed .app-sidebar { flex-basis: 70px; width: 70px; overflow: visible; }
        .sidebar-collapsed .app-brand { justify-content: center; padding: 0; }
        .sidebar-collapsed .app-brand-copy, .sidebar-collapsed .sidebar-toggle, .sidebar-collapsed .app-nav-link span:not(.nav-count), .sidebar-collapsed .nav-group > summary, .sidebar-collapsed .sidebar-user-copy, .sidebar-collapsed .sidebar-logout { display: none; }
        .sidebar-collapsed .app-nav { padding-right: 10px; padding-left: 10px; }
        .sidebar-collapsed .app-nav-link { position: relative; justify-content: center; padding: 9px; }
        .sidebar-collapsed .app-nav-link .ti { margin: 0; }
        .sidebar-collapsed .nav-group { margin: 6px 0; }
        .sidebar-collapsed .nav-group-links { margin: 0; }
        .sidebar-collapsed .sidebar-user-card { justify-content: center; padding: 7px; }
        .sidebar-collapsed .nav-count { position: absolute; top: 2px; right: 2px; }

        @media (max-width: 992px) {
            .dashboard-metrics, .dashboard-grid, .dashboard-chart-grid, .dashboard-chief-metrics { grid-template-columns: 1fr; }
            .app-shell { display: block; }
            .app-sidebar { position: fixed; z-index: 1060; width: 268px; transform: translateX(-100%); transition: transform .2s ease; box-shadow: 8px 0 24px rgba(24,36,51,.12); }
            .sidebar-open .app-sidebar { transform: translateX(0); }
            .app-content { padding: 16px; }
            .mobile-menu { display: grid !important; }
            .profile-menu span:not(.avatar) { display: none; }
            .catalog-heading { flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .dashboard-topline { align-items: stretch; flex-direction: column; }
            .dashboard-role-switch { width: 100%; }
            .dashboard-role-switch a { flex: 1; text-align: center; }
            .dashboard-metrics { gap: 10px; }
            .dashboard-shift-summary { grid-template-columns: 1fr; }
            .dashboard-shift-times { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .dashboard-update { grid-template-columns: 70px 30px minmax(0, 1fr); gap: 8px; }
            .app-content { padding: 12px; }
            .catalog-heading { padding: 12px; }
            .catalog-heading h1 { font-size: 19px; }
            .table-search { order: 3; width: 100%; }
            .create-icon-button { margin-left: 0; }
            .catalog-heading .table-search { flex-basis: 100%; width: 100%; }
            .data-table-wrap { overflow-x: auto; }
            .filter-fields label { grid-template-columns: 1fr; gap: 5px; }
            .filter-title, .filter-fields, .filter-actions { padding-right: 18px; padding-left: 18px; }
            .editor-details label, .editor-details label:nth-child(2) { grid-template-columns: 1fr; gap: 5px; }
            .additional-work-type-editor-details .additional-work-type-checkbox-row { grid-template-columns: minmax(0, 178px) auto !important; }
            .table-meta { grid-template-columns: 1fr; gap: 3px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div @class(['app-shell', 'app-shell--guest' => ! auth()->check()]) id="app-shell">
        @auth
            <aside class="app-sidebar">
                <div class="app-brand">
                    <a class="app-brand-copy" href="{{ route('workspace.dashboard') }}" aria-label="noElma"><span class="app-brand-no">no</span><span class="app-brand-elma">Elma</span></a>
                    <button class="sidebar-toggle d-none d-lg-grid" id="sidebar-toggle" type="button" title="Свернуть меню" aria-label="Свернуть меню"><i class="ti ti-layout-sidebar-left-collapse"></i></button>
                </div>
                <nav class="app-nav">
                    <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.dashboard')]) href="{{ route('workspace.dashboard') }}"><i class="ti ti-layout-dashboard"></i><span>Дашборд</span></a>
                    <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.my-tasks')]) href="{{ route('workspace.my-tasks') }}"><i class="ti ti-list-check"></i><span>Мои задачи</span><b class="nav-count">3</b></a>
                    <details class="nav-group" open>
                        <summary><span>Компания</span><i class="ti ti-chevron-down nav-group-chevron"></i></summary>
                        <div class="nav-group-links">
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.company-structure*')]) href="{{ route('workspace.company-structure') }}"><i class="ti ti-sitemap"></i><span>Структура компании</span></a>
                        </div>
                    </details>
                    <details class="nav-group" open>
                        <summary><span>4ДИ</span><i class="ti ti-chevron-down nav-group-chevron"></i></summary>
                        <div class="nav-group-links">
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.kvc')]) href="{{ route('workspace.kvc') }}"><i class="ti ti-building-factory-2"></i><span>КВЦ</span></a>
                        </div>
                    </details>
                    <details class="nav-group" open>
                        <summary><span>Отдел Тестирования</span><i class="ti ti-chevron-down nav-group-chevron"></i></summary>
                        <div class="nav-group-links">
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.testing')]) href="{{ route('workspace.testing') }}"><i class="ti ti-flask"></i><span>Тестирование</span></a>
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.strategies')]) href="{{ route('workspace.strategies') }}"><i class="ti ti-route"></i><span>Стратегии</span></a>
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.tests')]) href="{{ route('workspace.tests') }}"><i class="ti ti-clipboard-check"></i><span>Тесты</span></a>
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.tables*')]) href="{{ route('workspace.tables') }}"><i class="ti ti-table"></i><span>Таблицы</span></a>
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.templates')]) href="{{ route('workspace.templates') }}"><i class="ti ti-file-text"></i><span>Шаблоны</span></a>
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.additional-work')]) href="{{ route('workspace.additional-work') }}"><i class="ti ti-briefcase"></i><span>Доп. работы</span></a>
                            <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.additional-work-types*')]) href="{{ route('workspace.additional-work-types') }}"><i class="ti ti-tags"></i><span>Виды доп. работ</span></a>
                        </div>
                    </details>
                    <details class="nav-group" open>
                        <summary><span>Система</span><i class="ti ti-chevron-down nav-group-chevron"></i></summary>
                        <div class="nav-group-links">
                            @if(auth()->user()->isAdmin())
                                <a @class(['app-nav-link', 'active' => request()->routeIs('workspace.users.*')]) href="{{ route('workspace.users.index') }}">
                                    <i class="ti ti-users"></i><span>Пользователи</span>
                                </a>
                            @endif
                        </div>
                    </details>
                </nav>
                <div class="sidebar-user"><div class="sidebar-user-card"><x-user-avatar :user="auth()->user()" :size="36" /><span class="sidebar-user-copy"><span class="sidebar-user-name">{{ auth()->user()->displayName() }}</span><span class="sidebar-user-role">{{ auth()->user()::roles()[auth()->user()->role] }}</span></span><form method="POST" action="{{ route('logout') }}">@csrf<button class="sidebar-logout" type="submit" title="Выйти" aria-label="Выйти"><i class="ti ti-logout"></i></button></form></div></div>
            </aside>
        @endauth

        <main class="app-main">
            <section class="app-content"><div class="app-content-inner">@yield('content')</div></section>
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.catalog-data-table-script')
    <script>
        window.lucide?.createIcons();
        const appShell = document.getElementById('app-shell');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');

        if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth > 992) {
            appShell?.classList.add('sidebar-collapsed');
        }
        sidebarToggle?.addEventListener('click', () => {
            appShell?.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', appShell?.classList.contains('sidebar-collapsed') ? 'true' : 'false');
        });
        mobileMenuToggle?.addEventListener('click', () => appShell?.classList.toggle('sidebar-open'));

        document.addEventListener('click', (event) => {
            const serviceButton = event.target.closest('[data-modal-service-toggle]');
            if (serviceButton) {
                const dialog = serviceButton.closest('.app-modal__dialog');
                const service = dialog?.querySelector('[data-modal-service]');
                if (!service) return;
                const expanded = service.hidden;
                service.hidden = !expanded;
                serviceButton.setAttribute('aria-expanded', String(expanded));
                return;
            }

            const expandButton = event.target.closest('[data-modal-expand]');
            if (!expandButton) return;
            const dialog = expandButton.closest('.app-modal__dialog');
            if (!dialog) return;
            const expanded = dialog.classList.toggle('app-modal__dialog--expanded');
            expandButton.setAttribute('aria-pressed', String(expanded));
            expandButton.setAttribute('aria-label', expanded ? 'Свернуть окно' : 'Развернуть на весь экран');
            expandButton.setAttribute('title', expanded ? 'Свернуть окно' : 'Развернуть на весь экран');
            expandButton.innerHTML = `<i data-lucide="${expanded ? 'minimize-2' : 'maximize-2'}"></i>`;
            window.lucide?.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
