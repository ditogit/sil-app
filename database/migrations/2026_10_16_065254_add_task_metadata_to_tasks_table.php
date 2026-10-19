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
        Schema::table('tasks', function (Blueprint $table) {
            $table->json('tags')->nullable();
            $table->unsignedInteger('story_points')->nullable();
            $table->unsignedInteger('estimate_hours')->nullable();
            $table->time('due_time')->nullable();
        });

        // SQLite (testing) tidak punya CHECK bawaan enum Laravel.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE tasks DROP CONSTRAINT IF EXISTS tasks_priority_check');
            DB::statement("ALTER TABLE tasks ADD CONSTRAINT tasks_priority_check CHECK (priority IN ('low', 'medium', 'high', 'critical'))");
        }

        if (DB::getDriverName() === 'sqlite') {
            // SQLite tak bisa DROP CONSTRAINT: buat ulang kolom tanpa CHECK.
            // Aman karena database testing selalu fresh saat migrasi jalan.
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('priority', 10)->default('medium');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::table('tasks')->where('priority', 'critical')->update(['priority' => 'high']);
            DB::statement('ALTER TABLE tasks DROP CONSTRAINT IF EXISTS tasks_priority_check');
            DB::statement("ALTER TABLE tasks ADD CONSTRAINT tasks_priority_check CHECK (priority IN ('low', 'medium', 'high'))");
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['tags', 'story_points', 'estimate_hours', 'due_time']);
        });
    }
};
