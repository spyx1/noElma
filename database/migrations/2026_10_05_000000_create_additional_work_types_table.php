<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_work_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->decimal('execution_time', 10, 2)->nullable();
            $table->boolean('show_quantity')->default(false);
            $table->boolean('description_required')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_work_types');
    }
};
