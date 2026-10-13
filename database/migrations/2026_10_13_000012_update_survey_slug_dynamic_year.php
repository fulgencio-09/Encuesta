<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('surveys')
            ->where('slug', 'satisfaccion-usuario-2025')
            ->update([
                'slug' => 'satisfaccion-usuario',
                'name' => 'ENCUESTA DE SATISFACCIÓN DEL USUARIO',
            ]);
    }

    public function down(): void
    {
        DB::table('surveys')
            ->where('slug', 'satisfaccion-usuario')
            ->update([
                'slug' => 'satisfaccion-usuario-2025',
                'name' => 'ENCUESTA DE SATISFACCIÓN DEL USUARIO 2025',
            ]);
    }
};
