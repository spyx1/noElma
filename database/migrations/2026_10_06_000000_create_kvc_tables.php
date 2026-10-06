<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kvc_nodes', function (Blueprint $table): void {
            $table->string('id', 191)->primary();
            $table->string('parent_id', 191)->nullable()->index();
            $table->string('type', 32)->default('kvc-op');
            $table->string('type_name')->default('ОП / КВЦ');
            $table->string('title')->default('');
            $table->text('description')->nullable();
            $table->text('additional')->nullable();
            $table->string('indicator_type', 32)->default('percent');
            $table->decimal('range_from', 18, 4)->nullable();
            $table->decimal('range_to', 18, 4)->nullable();
            $table->string('true_label')->default('Да');
            $table->string('false_label')->default('Нет');
            $table->text('current_value')->nullable();
            $table->string('autoaudit')->nullable();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('owner_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('last_value_date')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('kvc_nodes')->cascadeOnDelete();
            $table->index(['parent_id', 'sort_order']);
        });

        Schema::create('kvc_node_users', function (Blueprint $table): void {
            $table->string('kvc_node_id', 191);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['kvc_node_id', 'user_id']);
            $table->foreign('kvc_node_id')->references('id')->on('kvc_nodes')->cascadeOnDelete();
        });

        Schema::create('kvc_node_meeting_managers', function (Blueprint $table): void {
            $table->string('kvc_node_id', 191);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['kvc_node_id', 'user_id']);
            $table->foreign('kvc_node_id')->references('id')->on('kvc_nodes')->cascadeOnDelete();
        });

        Schema::create('kvc_meetings', function (Blueprint $table): void {
            $table->string('id', 191)->primary();
            $table->string('kvc_node_id', 191)->index();
            $table->dateTime('date_time')->index();
            $table->text('summary')->nullable();
            $table->boolean('closed')->default(false)->index();
            $table->dateTime('closed_at')->nullable();
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('closed_by_name')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('updated_by_name')->nullable();
            $table->timestamps();
        });

        Schema::create('kvc_meeting_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('meeting_id', 191);
            $table->string('node_id', 191)->index();
            $table->string('node_name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('meeting_id')->references('id')->on('kvc_meetings')->cascadeOnDelete();
            $table->unique(['meeting_id', 'node_id']);
        });

        Schema::create('kvc_meeting_participants', function (Blueprint $table): void {
            $table->id();
            $table->string('meeting_id', 191);
            $table->string('section_node_id', 191)->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name');
            $table->string('previous_status', 32)->nullable();
            $table->string('attendance', 32)->nullable();
            $table->timestamps();

            $table->foreign('meeting_id')->references('id')->on('kvc_meetings')->cascadeOnDelete();
            $table->index(['meeting_id', 'section_node_id']);
            $table->unique(['meeting_id', 'section_node_id', 'user_id'], 'kvc_meeting_participant_user_unique');
        });

        Schema::create('kvc_meeting_participant_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('participant_id')->unique()->constrained('kvc_meeting_participants')->cascadeOnDelete();
            $table->text('current_obligation')->nullable();
            $table->text('current_result')->nullable();
            $table->text('current_comment')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('updated_by_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kvc_meeting_participant_values');
        Schema::dropIfExists('kvc_meeting_participants');
        Schema::dropIfExists('kvc_meeting_sections');
        Schema::dropIfExists('kvc_meetings');
        Schema::dropIfExists('kvc_node_meeting_managers');
        Schema::dropIfExists('kvc_node_users');
        Schema::dropIfExists('kvc_nodes');
    }
};
