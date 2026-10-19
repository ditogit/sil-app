<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->after('email');
            $table->string('position')->nullable()->after('nip');
            $table->foreignId('work_unit_id')->nullable()->after('position')->constrained('work_units')->nullOnDelete();
            $table->string('campus')->nullable()->after('work_unit_id');
            $table->string('avatar_url')->nullable()->after('campus');
            $table->string('status', 20)->default('active')->after('avatar_url');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('two_factor_confirmed_at');
        });

        // SQLite (testing) tidak punya CHECK bawaan enum Laravel.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('active', 'pending_2fa', 'suspended'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('work_unit_id');
            $table->dropColumn([
                'nip',
                'position',
                'campus',
                'avatar_url',
                'status',
                'two_factor_confirmed_at',
                'last_login_at',
            ]);
        });
    }
};
