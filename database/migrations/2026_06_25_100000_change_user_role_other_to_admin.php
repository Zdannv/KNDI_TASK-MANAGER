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
        // 1. Temporarily expand enum to allow both 'other' and 'admin'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('other', 'admin', 'pm', 'pg', 'co', 'ds') DEFAULT 'pg'");

        // 2. Update the role records
        DB::table('users')->where('role', 'other')->update(['role' => 'admin']);

        // 3. Shrink enum list to only allow 'admin'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pm', 'pg', 'co', 'ds') DEFAULT 'pg'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Expand enum to allow both 'other' and 'admin'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('other', 'admin', 'pm', 'pg', 'co', 'ds') DEFAULT 'pg'");

        // 2. Rollback the roles
        DB::table('users')->where('role', 'admin')->update(['role' => 'other']);

        // 3. Shrink back to the old enum definition
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('other', 'pm', 'pg', 'co', 'ds') DEFAULT 'pg'");
    }
};
