<!doctype html>
<html lang="es"><head><meta charset="utf-8"><style>
@page{margin:30px 25px}body{font-family:DejaVu Sans,Arial,sans-serif;font-size:9px;color:#222}h1{font-size:18px;text-align:center;color:#0f4c5c;margin:0 0 4px}h2{font-size:12px;color:#0f4c5c;margin:14px 0 6px}.subtitle{text-align:center;font-size:10px;margin-bottom:14px}.logo{width:70px;display:block;margin:0 auto 5px}table{width:100%;border-collapse:collapse;margin-bottom:10px}th,td{border:1px solid #777;padding:4px;text-align:center}th{background:#d9e5e9;font-weight:bold}td:first-child,th:first-child{text-align:left}.summary td{font-size:10px}.page-break{page-break-before:always}.small{font-size:8px;color:#555}.avoid{page-break-inside:avoid}
</style></head><body>
@if(file_exists(public_path('images/ese.png')))<img class="logo" src="{{ public_path('images/ese.png') }}">@endif
<h1>ENCUESTA DE SATISFACCIÓN DEL USUARIO {{ $year }}</h1><div class="subtitle">Informe estadístico — {{ $periodLabel }}<br>ESE Hospital Santa Teresa de Jesús de Ávila</div>

<h2>1. Resumen de resultados</h2><table class="summary"><tr><th>Total encuestas</th><th>Experiencia favorable</th><th>Promedio limpieza</th><th>Promedio servicios</th></tr><tr><td>{{ $responses->count() }}</td><td>{{ $favorable }}%</td><td>{{ $cleaning['average'] ?? '—' }}</td><td>{{ $matrixStats['average'] ?? '—' }}</td></tr></table>

@foreach($questions->whereIn('type',['single_choice','scale']) as $question)
<div class="avoid"><h2>{{ $question->position }}. {{ $question->question_text }}</h2><table><thead><tr><th>Mes</th><th>Total</th>@foreach($question->options as $option)<th>{{ $option->label }}</th>@endforeach</tr></thead><tbody>@foreach($monthly as $row)<tr><td>{{ $row['month'] }}</td><td>{{ $row['total'] }}</td>@foreach($question->options as $option)<td>{{ $row['questions'][$question->question_key][$option->value] ?? 0 }}</td>@endforeach</tr>@endforeach<tr><th>TOTAL</th><th>{{ $responses->count() }}</th>@foreach($question->options as $option)<th>{{ $questionStats->firstWhere('question.id',$question->id)['options']->firstWhere('label',$option->label)['count'] ?? 0 }}</th>@endforeach</tr></tbody></table></div>
@endforeach

<div class="page-break"></div><h2>2. Registro de detalle de satisfacción por servicio</h2>
@if($matrixStats)<table><thead><tr><th>Servicio</th><th>Total</th><th>Promedio</th>@foreach($matrixStats['question']->options as $option)<th>{{ $option->label }}</th>@endforeach</tr></thead><tbody>@foreach($matrixStats['items'] as $item)<tr><td>{{ $item['label'] }}</td><td>{{ $item['total'] }}</td><td>{{ $item['average'] ?? '—' }}</td>@foreach($item['options'] as $option)<td>{{ $option['count'] }}</td>@endforeach</tr>@endforeach</tbody></table>@endif

<h2>3. Sugerencias y recomendaciones</h2>@if($suggestions->count())<table><thead><tr><th>Fecha</th><th>Observación</th></tr></thead><tbody>@foreach($suggestions as $s)<tr><td>{{ $s['date'] }}</td><td style="text-align:left">{{ $s['text'] }}</td></tr>@endforeach</tbody></table>@else<p>No se registraron sugerencias en el periodo.</p>@endif

<p class="small">Generado por el sistema de encuestas. Periodo: {{ $periodLabel }}. Total de respuestas: {{ $responses->count() }}.</p>
</body></html>
