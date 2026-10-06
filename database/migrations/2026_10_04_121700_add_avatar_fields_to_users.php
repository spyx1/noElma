<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::table('users', function (Blueprint $table): void { $table->string('avatar_path')->nullable()->after('phone'); $table->string('avatar_mode')->default('initials')->after('avatar_path'); }); }
    public function down(): void { Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['avatar_path', 'avatar_mode'])); }
};
