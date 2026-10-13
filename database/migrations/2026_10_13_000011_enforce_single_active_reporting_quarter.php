<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // RN-TRI-01: nunca puede existir más de un trimestre activo simultáneamente.
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('CREATE UNIQUE INDEX reporting_quarters_only_one_active ON reporting_quarters (is_active) WHERE is_active = 1');
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX reporting_quarters_only_one_active ON reporting_quarters (is_active) WHERE is_active = true');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite' || DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS reporting_quarters_only_one_active');
        }
    }
};
