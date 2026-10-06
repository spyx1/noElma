<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reference_tables', function (Blueprint $table): void {
            $table->uuid('external_id')->nullable()->unique()->after('id');
            $table->json('default_values')->nullable()->after('script');
            $table->json('default_result_values')->nullable()->after('default_values');
            $table->json('source_json')->nullable()->after('default_result_values');
        });

        Schema::table('reference_table_columns', function (Blueprint $table): void {
            $table->string('section')->default('primary')->after('reference_table_id');
            $table->string('elma_type')->nullable()->after('data_type');
            $table->boolean('is_readonly')->default(false)->after('is_required');
            $table->json('settings')->nullable()->after('values');
        });
    }

    public function down(): void
    {
        Schema::table('reference_table_columns', function (Blueprint $table): void {
            $table->dropColumn(['section', 'elma_type', 'is_readonly', 'settings']);
        });

        Schema::table('reference_tables', function (Blueprint $table): void {
            $table->dropUnique(['external_id']);
            $table->dropColumn(['external_id', 'default_values', 'default_result_values', 'source_json']);
        });
    }
};
