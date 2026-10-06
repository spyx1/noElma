@php($settings = (array) ($column->settings ?? []))
@if ($column->elma_type === 'select')
    <select name="{{ $name }}">
        @foreach ($settings['list'] ?? [] as $option)
            @if (trim((string) $option) !== '')
                <option value="{{ $option }}" @selected($value === $option)>{{ $option }}</option>
            @endif
        @endforeach
    </select>
@elseif ($column->elma_type === 'boolean')
    <select name="{{ $name }}"><option value="1" @selected((string) $value === '1')>{{ $settings['b1_text'] ?? 'Да' }}</option><option value="0" @selected((string) $value === '0')>{{ $settings['b0_text'] ?? 'Нет' }}</option></select>
@elseif ($column->elma_type === 'files')
    <button type="button" class="file-value-button" title="Загрузка файлов будет доступна при выполнении теста"><i data-lucide="paperclip"></i> Файлы</button><input type="hidden" name="{{ $name }}" value="{{ $value }}">
@elseif ($column->elma_type === 'inc')
    <span class="inc-value">{{ $rowNumber }}</span><input type="hidden" name="{{ $name }}" value="{{ $rowNumber }}">
@else
    <input name="{{ $name }}" value="{{ $value }}" @readonly($column->is_readonly)>
@endif
