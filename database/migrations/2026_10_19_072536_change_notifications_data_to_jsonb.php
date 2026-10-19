<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->changeDataType('jsonb');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->changeDataType('text');
    }

    private function changeDataType(string $type): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'pgsql') {
            return;
        }

        $current = $connection->selectOne(
            'select data_type from information_schema.columns where table_name = ? and column_name = ?',
            ['notifications', 'data'],
        );

        if ($current?->data_type === $type) {
            return;
        }

        $connection->statement(
            "alter table \"notifications\" alter column \"data\" type {$type} using \"data\"::{$type}",
        );
    }
};
