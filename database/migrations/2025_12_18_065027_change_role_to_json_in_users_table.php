<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add temporary JSON column
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles_temp')->nullable()->after('role');
        });

        // 2. Migrate data: Convert string role to JSON array
        $users = DB::table('users')->select('id', 'role')->get();

        foreach ($users as $user) {
            $roles = [$user->role]; // Make it an array
            DB::table('users')
                ->where('id', $user->id)
                ->update(['roles_temp' => json_encode($roles)]);
        }

        // 3. Drop old column and rename new one
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('roles_temp', 'role');
        });

        // Make non-nullable effectively (optional, based on requirement)
        Schema::table('users', function (Blueprint $table) {
            $table->json('role')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add temporary string column
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_temp')->nullable()->after('role');
        });

        // 2. Migrate data: Take first role from Array
        $users = DB::table('users')->select('id', 'role')->get();

        foreach ($users as $user) {
            $roles = json_decode($user->role);
            $primaryRole = $roles[0] ?? 'Staff'; // Default fallback
            DB::table('users')
                ->where('id', $user->id)
                ->update(['role_temp' => $primaryRole]);
        }

        // 3. Drop JSON column and rename
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_temp', 'role');
        });
    }
};
