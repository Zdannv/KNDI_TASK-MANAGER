<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any users with the old role to the new one
        DB::table('users')->where('role', 'other')->update(['role' => 'admin']);

        // Modify the enum column structure
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pm', 'pg', 'co', 'ds') DEFAULT 'pg'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Modify back to support 'other'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('other', 'pm', 'pg', 'co', 'ds') DEFAULT 'pg'");

        // Reverse back
        DB::table('users')->where('role', 'admin')->update(['role' => 'other']);
    }
};
