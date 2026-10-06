@if (session('status'))
    <div class="toast alert alert-success alert-dismissible" id="toast" role="status">
        <i class="ti ti-circle-check me-2"></i>{{ session('status') }}
        <button class="btn-close" type="button" aria-label="Закрыть" onclick="this.parentElement.remove()"></button>
    </div>
    <script>
        window.setTimeout(() => document.getElementById('toast')?.classList.add('is-hidden'), 3000);
    </script>
@endif
