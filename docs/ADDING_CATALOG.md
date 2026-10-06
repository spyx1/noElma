# Как добавить новый справочник

## Что уже общее

- `resources/views/components/catalog-page.blade.php` — заголовок страницы, кнопка создания и расположение панели таблицы.
- `resources/views/components/data-table.blade.php` — таблица DataTables, поиск, фильтр, выбор столбцов, изменение ширины, Excel, массовое удаление и кнопка «наверх».
- `resources/views/components/catalog-data-table-script.blade.php` — единый читаемый JavaScript для всех `x-data-table`.
- `resources/views/components/flash-status.blade.php` — уведомление после создания, обновления или удаления.

Не копируйте JavaScript, CSS панели или разметку фильтра в новый справочник: их подключает `x-data-table`.

## Минимальный набор файлов

1. Миграция `database/migrations/*_create_<entities>_table.php`.
2. Модель `app/Models/<Entity>.php`.
3. Контроллер `app/Http/Controllers/<Entity>Controller.php`.
4. Представления `resources/views/<entities>/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`.
5. Маршруты в `routes/web.php`.

## Шаблон страницы списка

```blade
@extends('layouts.app', ['title' => 'Изделия'])

@section('content')
    <x-catalog-page title="Изделия" :create-route="route('workspace.items.create')" create-label="Создать изделие">
        <x-flash-status />

        <x-data-table
            id="items"
            :records-count="$items->count()"
            search-placeholder="Поиск по названию"
            :columns="['name' => 'Название', 'code' => 'Код', 'updated' => 'Последнее редактирование']"
            :filter-fields="[
                'name' => ['label' => 'Название'],
                'code' => ['label' => 'Код'],
                'updated' => ['label' => 'Последнее редактирование', 'type' => 'date'],
            ]"
            :default-visible="['name', 'code', 'updated']"
            :bulk-delete-route="route('workspace.items.bulk-destroy')"
            catalog-kind="standard"
        >
            @forelse ($items as $item)
                <tr>
                    <td class="select-column"><input class="row-select" type="checkbox" value="{{ $item->id }}" aria-label="Выбрать изделие"></td>
                    <td data-column="name"><a href="{{ route('workspace.items.show', $item) }}">{{ $item->name }}</a></td>
                    <td data-column="code">{{ $item->code }}</td>
                    <td data-column="updated" data-filter-value="{{ $item->updated_at->format('Y-m-d') }}">{{ $item->updated_at->format('d.m.Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Изделий пока нет.</td></tr>
            @endforelse
        </x-data-table>
    </x-catalog-page>
@endsection
```

## Правила для строк

- Ключ `data-column` в ячейке должен совпадать с ключом в `columns`.
- Для фильтра по дате задавайте `data-filter-value` в формате `Y-m-d`.
- Для фильтра по связанному пользователю задавайте ID в `data-filter-value`, а в `filter-fields` передавайте `type => 'user'` и массив `id => ФИО`.
- Первый столбец — флажок выбора — добавляется только если указан `bulk-delete-route`.

## Проверка после изменений

```sh
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan route:list --path=items
```
