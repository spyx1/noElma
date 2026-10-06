# Промт для ChatGPT: новый справочник

Скопируйте текст ниже в новый чат и замените данные в квадратных скобках.

```text
У меня Laravel-приложение «Отдел тестирования». Нужно добавить новый справочник «[НАЗВАНИЕ]».

Архитектура проекта:
- Laravel, Blade, Eloquent, обычные web-маршруты.
- Общий layout: resources/views/layouts/app.blade.php.
- Общая страница справочника: resources/views/components/catalog-page.blade.php.
- Общая интерактивная таблица: resources/views/components/data-table.blade.php.
- Единое поведение таблиц: resources/views/components/catalog-data-table-script.blade.php.
- Инструкция и рабочий пример: docs/ADDING_CATALOG.md.
- Образец существующего справочника: app/Http/Controllers/ReferenceTableController.php и resources/views/tables/index.blade.php.

Требования к новому справочнику:
1. Создай миграцию, модель, контроллер, маршруты и Blade-представления index/create/edit/show.
2. В index обязательно используй <x-catalog-page>, <x-flash-status> и <x-data-table>; не копируй JavaScript, фильтры, панель столбцов, экспорт Excel и CSS таблицы.
3. Колонки: [СПИСОК: ключ — заголовок — тип — фильтр].
4. Поля формы: [СПИСОК ПОЛЕЙ, ОБЯЗАТЕЛЬНОСТЬ, ТИПЫ, СВЯЗИ].
5. Нужны операции: [создание/просмотр/редактирование/удаление/массовое удаление].
6. Добавь пункт в левое меню [ДА/НЕТ, раздел меню].
7. Сохрани стиль и русские подписи существующего приложения.
8. В конце перечисли изменённые файлы и команды проверки. Не меняй существующую общую логику таблиц без необходимости.

Сначала кратко опиши план и список файлов, затем внеси изменения.
```

## Что приложить к чату

Обязательно приложите:

1. `docs/ADDING_CATALOG.md`.
2. `resources/views/components/data-table.blade.php`.
3. `resources/views/components/catalog-page.blade.php`.
4. `resources/views/components/catalog-data-table-script.blade.php`.
5. Пример: `app/Http/Controllers/ReferenceTableController.php` и `resources/views/tables/index.blade.php`.
6. `routes/web.php`.

Если справочник связан с пользователями, дополнительно приложите `app/Models/User.php` и `resources/views/components/user-avatar.blade.php`.
