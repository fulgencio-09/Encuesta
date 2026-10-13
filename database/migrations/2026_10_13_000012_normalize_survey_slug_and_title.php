<?php

use App\Models\Survey;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $survey = Survey::where('slug', 'satisfaccion-usuario-2025')->first();

        if ($survey) {
            $survey->update([
                'slug' => 'satisfaccion-usuario',
                'name' => 'ENCUESTA DE SATISFACCIÓN DEL USUARIO',
            ]);
        }
    }

    public function down(): void
    {
        $survey = Survey::where('slug', 'satisfaccion-usuario')->first();

        if ($survey) {
            $survey->update([
                'slug' => 'satisfaccion-usuario-2025',
                'name' => 'ENCUESTA DE SATISFACCIÓN DEL USUARIO 2025',
            ]);
        }
    }
};
