<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_tables', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('row_count')->default(1);
            $table->text('script')->nullable();
            $table->timestamps();
        });

        Schema::create('reference_table_columns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reference_table_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('data_type')->default('Строка');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_key')->default(false);
            $table->unsignedInteger('width')->default(175);
            $table->json('values')->nullable();
            $table->unsignedInteger('position');
            $table->timestamps();
        });

        $now = now();
        $tableId = DB::table('reference_tables')->insertGetId([
            'name' => 'Тест термозащиты (начальные условия) БП',
            'row_count' => 1,
            'script' => '-- Скрипт для расчёта начальных условий',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach ([
            ['Uin, B', 'Строка', true, true, '{230}'],
            ['Uout, B', 'Строка', true, true, '{12}'],
            ['Iout, A', 'Строка', true, true, '{3.5}'],
            ['Uвых до прогрева, B', 'Число дробное', true, false, ''],
            ['КПД до прогрева, %', 'Число дробное', true, false, ''],
        ] as $position => [$name, $type, $required, $key, $value]) {
            DB::table('reference_table_columns')->insert([
                'reference_table_id' => $tableId,
                'name' => $name,
                'data_type' => $type,
                'is_required' => $required,
                'is_key' => $key,
                'width' => 175,
                'values' => json_encode([$value]),
                'position' => $position + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_table_columns');
        Schema::dropIfExists('reference_tables');
    }
};
