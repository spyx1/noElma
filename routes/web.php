<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdditionalWorkTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyStructureController;
use App\Http\Controllers\KvcController;
use App\Http\Controllers\ReferenceTableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('workspace.dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->name('workspace.')->group(function (): void {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::view('/my-tasks', 'admin.section', [
        'title' => 'Мои задачи',
    ])->name('my-tasks');

    /*
    |--------------------------------------------------------------------------
    | Компания
    |--------------------------------------------------------------------------
    */

    Route::get('/company-structure', [CompanyStructureController::class, 'index'])
        ->name('company-structure');

    Route::get('/company-structure/export', [CompanyStructureController::class, 'export'])
        ->name('company-structure.export');

    Route::post('/company-structure/nodes', [CompanyStructureController::class, 'store'])
        ->name('company-structure.nodes.store');

    Route::put('/company-structure/nodes/{node}', [CompanyStructureController::class, 'update'])
        ->name('company-structure.nodes.update');

    Route::delete('/company-structure/nodes/{node}', [CompanyStructureController::class, 'destroy'])
        ->name('company-structure.nodes.destroy');

    Route::view('/employees', 'admin.section', [
        'title' => 'Сотрудники',
    ])->name('employees');

    /*
    |--------------------------------------------------------------------------
    | КВЦ
    |--------------------------------------------------------------------------
    */

    Route::get('/kvc', [KvcController::class, 'index'])
        ->name('kvc');

    Route::prefix('/kvc/api')->name('kvc.api.')->group(function (): void {
        Route::get('/tree', [KvcController::class, 'tree'])
            ->name('tree');

        Route::put('/tree', [KvcController::class, 'updateTree'])
            ->name('tree.update');

        Route::patch('/nodes/{nodeId}/value', [KvcController::class, 'updateValue'])
            ->name('nodes.value');

        Route::get('/nodes/{nodeId}/meetings', [KvcController::class, 'meetings'])
            ->name('meetings.index');

        Route::post('/nodes/{nodeId}/meetings/save', [KvcController::class, 'saveMeeting'])
            ->name('meetings.save');

        Route::delete('/nodes/{nodeId}/meetings/{meetingId}', [KvcController::class, 'deleteMeeting'])
            ->name('meetings.destroy');

        Route::patch('/nodes/{nodeId}/meetings/{meetingId}/participant', [KvcController::class, 'updateParticipant'])
            ->name('participants.update');

        Route::delete('/nodes/{nodeId}/meetings/{meetingId}/participant', [KvcController::class, 'deleteParticipant'])
            ->name('participants.destroy');
    });

    Route::view('/additional-work', 'admin.section', [
        'title' => 'Доп. работы',
    ])->name('additional-work');

    Route::view('/testing', 'admin.section', [
        'title' => 'Тестирование',
    ])->name('testing');

    Route::view('/strategies', 'admin.section', [
        'title' => 'Стратегии',
    ])->name('strategies');

    Route::view('/tests', 'admin.section', [
        'title' => 'Тесты',
    ])->name('tests');

    /*
    |--------------------------------------------------------------------------
    | Таблицы
    |--------------------------------------------------------------------------
    */

    Route::get('/tables', [ReferenceTableController::class, 'index'])
        ->name('tables');

    Route::get('/tables/selection-ids', [ReferenceTableController::class, 'selectionIds'])
        ->name('tables.selection-ids');

    Route::get('/tables/create', [ReferenceTableController::class, 'create'])
        ->name('tables.create');

    Route::post('/tables', [ReferenceTableController::class, 'store'])
        ->name('tables.store');

    Route::delete('/tables', [ReferenceTableController::class, 'bulkDestroy'])
        ->name('tables.bulk-destroy');

    Route::get('/tables/{referenceTable}', [ReferenceTableController::class, 'show'])
        ->name('tables.show');

    Route::get('/tables/{referenceTable}/edit', [ReferenceTableController::class, 'edit'])
        ->name('tables.edit');

    Route::put('/tables/{referenceTable}', [ReferenceTableController::class, 'update'])
        ->name('tables.update');

    Route::delete('/tables/{referenceTable}', [ReferenceTableController::class, 'destroy'])
        ->name('tables.destroy');

    Route::view('/templates', 'admin.section', [
        'title' => 'Шаблоны',
    ])->name('templates');

    /*
    |--------------------------------------------------------------------------
    | Виды доп. работ
    |--------------------------------------------------------------------------
    */

    Route::get('/additional-work-types', [AdditionalWorkTypeController::class, 'index'])
        ->name('additional-work-types');

    Route::get('/additional-work-types/create', [AdditionalWorkTypeController::class, 'create'])
        ->name('additional-work-types.create');

    Route::post('/additional-work-types', [AdditionalWorkTypeController::class, 'store'])
        ->name('additional-work-types.store');

    Route::delete('/additional-work-types', [AdditionalWorkTypeController::class, 'bulkDestroy'])
        ->name('additional-work-types.bulk-destroy');

    Route::get('/additional-work-types/{additionalWorkType}', [AdditionalWorkTypeController::class, 'show'])
        ->name('additional-work-types.show');

    Route::get('/additional-work-types/{additionalWorkType}/edit', [AdditionalWorkTypeController::class, 'edit'])
        ->name('additional-work-types.edit');

    Route::put('/additional-work-types/{additionalWorkType}', [AdditionalWorkTypeController::class, 'update'])
        ->name('additional-work-types.update');

    Route::delete('/additional-work-types/{additionalWorkType}', [AdditionalWorkTypeController::class, 'destroy'])
        ->name('additional-work-types.destroy');

    /*
    |--------------------------------------------------------------------------
    | Пользователи
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function (): void {
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/selection-ids', [UserController::class, 'selectionIds'])
            ->name('users.selection-ids');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::delete('/users', [UserController::class, 'bulkDestroy'])
            ->name('users.bulk-destroy');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });

    // Карточка пользователя доступна всем авторизованным сотрудникам.
    // Маршрут расположен после /users/create, чтобы "create" не попал в {user}.
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('users.show');
});
