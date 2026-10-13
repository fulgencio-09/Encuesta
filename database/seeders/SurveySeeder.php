<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Survey;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        $survey = Survey::updateOrCreate(
            ['slug' => 'satisfaccion-usuario-2025'],
            [
                'name' => 'ENCUESTA DE SATISFACCIÓN DEL USUARIO 2025',
                'description' => 'Encuesta confidencial para conocer el nivel de satisfacción con la atención recibida en los distintos servicios que ofrece la ESE Hospital Santa Teresa de Jesús de Ávila.',
                'is_active' => true,
                'allow_multiple_responses' => true,
            ]
        );

        $questions = [
            [
                'question_key' => 'experiencia_global',
                'question_text' => '¿Cómo calificaría su experiencia global respecto a los servicios de salud que ha recibido en la ESE Hospital Santa Teresa de Jesús de Ávila?',
                'type' => 'single_choice',
                'position' => 1,
                'is_required' => true,
                'options' => [
                    ['label' => 'Muy Buena', 'value' => 'muy_buena'],
                    ['label' => 'Buena', 'value' => 'buena'],
                    ['label' => 'Regular', 'value' => 'regular'],
                    ['label' => 'Mala', 'value' => 'mala'],
                    ['label' => 'No Responde', 'value' => 'no_responde'],
                ],
            ],
            [
                'question_key' => 'recomendaria',
                'question_text' => '¿Recomendaría a sus familiares y amigos a la ESE Hospital Santa Teresa de Jesús de Ávila?',
                'type' => 'single_choice',
                'position' => 2,
                'is_required' => true,
                'options' => [
                    ['label' => 'Definitivamente Si', 'value' => 'definitivamente_si'],
                    ['label' => 'Probablemente Si', 'value' => 'probablemente_si'],
                    ['label' => 'Definitivamente No', 'value' => 'definitivamente_no'],
                    ['label' => 'Probablemente No', 'value' => 'probablemente_no'],
                    ['label' => 'No Responde', 'value' => 'no_responde'],
                ],
            ],
            [
                'question_key' => 'eficiencia_administrativa',
                'question_text' => '¿Cómo califica la eficiencia del área administrativa en la gestión y respuesta a sus trámites?',
                'type' => 'single_choice',
                'position' => 3,
                'is_required' => true,
                'options' => [
                    ['label' => 'Muy Buena', 'value' => 'muy_buena'],
                    ['label' => 'Buena', 'value' => 'buena'],
                    ['label' => 'Regular', 'value' => 'regular'],
                    ['label' => 'Mala', 'value' => 'mala'],
                    ['label' => 'No Responde', 'value' => 'no_responde'],
                ],
            ],
            [
                'question_key' => 'frecuencia_asistencia',
                'question_text' => '¿Con que frecuencia asiste usted a la ESE Hospital Santa Teresa de Jesús de Ávila?',
                'type' => 'single_choice',
                'position' => 4,
                'is_required' => true,
                'options' => [
                    ['label' => 'Semanal', 'value' => 'semanal'],
                    ['label' => 'Mensual', 'value' => 'mensual'],
                    ['label' => 'Dos Veces al Año', 'value' => 'dos_veces_al_ano'],
                    ['label' => 'Una Vez al Año', 'value' => 'una_vez_al_ano'],
                    ['label' => 'No Responde', 'value' => 'no_responde'],
                ],
            ],
            [
                'question_key' => 'claridad_profesional',
                'question_text' => '¿Considera usted que, al momento de la consulta, el profesional explica de manera clara su diagnóstico y el tratamiento a seguir?',
                'type' => 'single_choice',
                'position' => 5,
                'is_required' => true,
                'options' => [
                    ['label' => 'Si, Le Quedo Claro', 'value' => 'si_le_quedo_claro'],
                    ['label' => 'No, Le Quedo Claro', 'value' => 'no_le_quedo_claro'],
                    ['label' => 'El Profesional No fue Claro', 'value' => 'profesional_no_fue_claro'],
                    ['label' => 'No Responde', 'value' => 'no_responde'],
                ],
            ],
            [
                'question_key' => 'oportunidad_citas',
                'question_text' => '¿Cómo califica usted la oportunidad en la asignación de citas?',
                'type' => 'single_choice',
                'position' => 6,
                'is_required' => true,
                'options' => [
                    ['label' => 'Muy Buena', 'value' => 'muy_buena'],
                    ['label' => 'Buena', 'value' => 'buena'],
                    ['label' => 'Regular', 'value' => 'regular'],
                    ['label' => 'Mala', 'value' => 'mala'],
                    ['label' => 'Muy Mala', 'value' => 'muy_mala'],
                    ['label' => 'No Responde', 'value' => 'no_responde'],
                ],
            ],
            [
                'question_key' => 'atencion_servicios',
                'question_text' => 'Como califica usted la atención que recibió en los siguientes servicios',
                'type' => 'matrix',
                'position' => 7,
                'is_required' => true,
                'settings' => ['scale_min' => 1, 'scale_max' => 5],
                'options' => [
                    ['label' => '1', 'value' => '1'],
                    ['label' => '2', 'value' => '2'],
                    ['label' => '3', 'value' => '3'],
                    ['label' => '4', 'value' => '4'],
                    ['label' => '5', 'value' => '5'],
                    ['label' => 'No Responde', 'value' => 'no_responde'],
                ],
                'items' => [
                    ['label' => 'Asignacion de Citas', 'value' => 'asignacion_de_citas'],
                    ['label' => 'Medicina General', 'value' => 'medicina_general'],
                    ['label' => 'Odontologia', 'value' => 'odontologia'],
                    ['label' => 'Toma de Muestras de Laboratorio', 'value' => 'toma_de_muestras_de_laboratorio'],
                    ['label' => 'Ruta de Promoción y Mantenimiento', 'value' => 'ruta_de_promocion_y_mantenimiento'],
                    ['label' => 'Urgencias', 'value' => 'urgencias'],
                    ['label' => 'Hospitalizacion', 'value' => 'hospitalizacion'],
                    ['label' => 'Rayos X e Imagenologia', 'value' => 'rayos_x_e_imagenologia'],
                    ['label' => 'Fisoterapia', 'value' => 'fisoterapia'],
                    ['label' => 'Nutricion', 'value' => 'nutricion'],
                    ['label' => 'Psicologia', 'value' => 'psicologia'],
                    ['label' => 'Trabajo Social', 'value' => 'trabajo_social'],
                ],
            ],
            [
                'question_key' => 'limpieza_desinfeccion',
                'question_text' => '¿Cómo evalúa la limpieza y desinfección en las áreas de la ESE Hospital Santa Teresa de Jesús de Ávila?',
                'type' => 'scale',
                'position' => 8,
                'is_required' => true,
                'settings' => ['scale_min' => 1, 'scale_max' => 5],
                'options' => [
                    ['label' => '1', 'value' => '1'],
                    ['label' => '2', 'value' => '2'],
                    ['label' => '3', 'value' => '3'],
                    ['label' => '4', 'value' => '4'],
                    ['label' => '5', 'value' => '5'],
                ],
            ],
            [
                'question_key' => 'sugerencias',
                'question_text' => '¿Qué sugerencia o recomendaciones daría usted con el fin de mejorar la prestación del servicio en la institución?',
                'type' => 'textarea',
                'position' => 9,
                'is_required' => false,
            ],
        ];

        foreach ($questions as $definition) {
            $options = $definition['options'] ?? [];
            $items = $definition['items'] ?? [];
            unset($definition['options'], $definition['items']);

            $question = Question::updateOrCreate(
                ['survey_id' => $survey->id, 'question_key' => $definition['question_key']],
                $definition
            );

            $question->options()->delete();
            foreach ($options as $position => $option) {
                $question->options()->create([
                    'label' => $option['label'],
                    'value' => $option['value'],
                    'position' => $position + 1,
                    'is_active' => true,
                ]);
            }

            $question->items()->delete();
            foreach ($items as $position => $item) {
                $question->items()->create([
                    'label' => $item['label'],
                    'value' => $item['value'],
                    'position' => $position + 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}
