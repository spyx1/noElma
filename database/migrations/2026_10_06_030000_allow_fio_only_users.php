<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Сотрудники из оргструктуры могут существовать в системе только по ФИО,
            // без логина и пароля.
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();

            // Гарантируем строковый тип роли, чтобы использовать роль employee.
            $table->string('role', 32)->default('tester')->change();
        });
    }

    public function down(): void
    {
        // Перед возвратом NOT NULL выдаём технические реквизиты записям без учётной записи.
        DB::table('users')
            ->whereNull('email')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $user): void {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'email' => 'rollback-user-' . $user->id . '@local.invalid',
                        'password' => Hash::make(Str::random(48)),
                        'role' => 'tester',
                    ]);
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
            $table->string('role', 32)->default('tester')->change();
        });
    }
};
