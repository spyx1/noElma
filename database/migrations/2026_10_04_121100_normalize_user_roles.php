<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'manager')->update(['role' => 'head_ot']);
        DB::table('users')->where('role', 'employee')->update(['role' => 'tester']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'head_ot')->update(['role' => 'manager']);
        DB::table('users')->where('role', 'tester')->update(['role' => 'employee']);
    }
};
