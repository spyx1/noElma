<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $userId = DB::table('users')->value('id');
        $now = now();
        $rows = [];

        for ($number = 1; $number <= 500; $number++) {
            $name = sprintf('Тестовая таблица для проверки №%03d', $number);
            if (!DB::table('reference_tables')->where('name', $name)->exists()) {
                $rows[] = [
                    'name' => $name,
                    'row_count' => ($number % 5) + 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('reference_tables')->insert($chunk);
        }
    }

    public function down(): void {}
};
