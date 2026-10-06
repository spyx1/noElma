<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_structure_nodes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('company_structure_nodes')
                ->cascadeOnDelete();
            $table->string('title');
            $table->boolean('allows_multiple_users')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });

        Schema::create('company_structure_node_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_structure_node_id')
                ->constrained('company_structure_nodes')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['company_structure_node_id', 'user_id'], 'company_structure_node_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_structure_node_user');
        Schema::dropIfExists('company_structure_nodes');
    }
};
