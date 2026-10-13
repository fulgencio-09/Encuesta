<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Regla de negocio RN-TRI-01:
        // nunca puede existir más de un trimestre activo simultáneamente.
        // SQLite y PostgreSQL permiten un índice único parcial.
        if (DB::getDriverName() === 'sqlite') {
            DB::statement(
                'CREATE UNIQUE INDEX reporting_quarters_only_one_active\n                 ON reporting_quarters (is_active)\n                 WHERE is_active = 1'
            );
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'CREATE UNIQUE INDEX reporting_quarters_only_one_active\n                 ON reporting_quarters (is_active)\n                 WHERE is_active = true'
            );
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite' || DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS reporting_quarters_only_one_active');
        }
    }
};
