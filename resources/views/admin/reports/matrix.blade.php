<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Matriz estadística</title>
<style>
body{margin:0;font-family:Arial,sans-serif;background:#f3f6f8;color:#111827}.wrap{max-width:1400px;margin:20px auto;padding:0 14px}.head{background:#fff;padding:20px;border-radius:12px;margin-bottom:16px}h1,h2{color:#0f4c5c}.table-wrap{overflow:auto;background:#fff;margin-bottom:22px}table{border-collapse:collapse;width:100%;font-size:12px}th,td{border:1px solid #777;padding:7px;text-align:center;vertical-align:middle}th{background:#d9e5e9}td:first-child,th:first-child{text-align:left;white-space:nowrap}.subhead{background:#eef4f6;font-weight:700}.actions{margin-bottom:18px;display:flex;gap:10px;flex-wrap:wrap}.btn{display:inline-block;padding:11px 16px;border-radius:8px;background:#0f4c5c;color:#fff;text-decoration:none;font-weight:700}.btn.light{background:#19728a}.note{color:#64748b}
</style></head><body><div class="wrap">
<div class="head"><h1>Matriz estadística de satisfacción</h1><p><strong>Periodo:</strong> {{ $periodLabel }}</p><p class="note">La matriz presenta cantidades de respuestas por mes y por opción. Las preguntas de selección se muestran como bloques independientes y la matriz de servicios se presenta al final.</p><div class="actions"><a class="btn" href="{{ route('admin.reports.index', request()->query()) }}">← Regresar a informes</a><a class="btn light" href="{{ route('admin.reports.pdf', request()->query()) }}">Descargar PDF</a></div></div>

@foreach($questions->whereIn('type',['single_choice','scale']) as $question)
<div class="table-wrap"><h2>{{ $question->position }}. {{ $question->question_text }}</h2><table><thead><tr><th>Mes</th><th>Total</th>@foreach($question->options as $option)<th>{{ $option->label }}</th>@endforeach</tr></thead><tbody>@foreach($monthly as $row)<tr><td>{{ $row['month'] }}</td><td>{{ $row['total'] }}</td>@foreach($question->options as $option)<td>{{ $row['questions'][$question->question_key][$option->value] ?? 0 }}</td>@endforeach</tr>@endforeach<tr class="subhead"><td>TOTAL</td><td>{{ $responses->count() }}</td>@foreach($question->options as $option)<td>{{ $questionStats->firstWhere('question.id',$question->id)['options']->firstWhere('label',$option->label)['count'] ?? 0 }}</td>@endforeach</tr></tbody></table></div>
@endforeach

@if($matrixStats)
<div class="table-wrap"><h2>{{ $matrixStats['question']->position }}. {{ $matrixStats['question']->question_text }}</h2><table><thead><tr><th>Servicio</th><th>Total</th><th>Promedio</th>@foreach($matrixStats['question']->options as $option)<th>{{ $option->label }}</th>@endforeach</tr></thead><tbody>@foreach($matrixStats['items'] as $item)<tr><td>{{ $item['label'] }}</td><td>{{ $item['total'] }}</td><td>{{ $item['average'] ?? '—' }}</td>@foreach($item['options'] as $option)<td>{{ $option['count'] }}</td>@endforeach</tr>@endforeach</tbody></table></div>
@endif

<div class="table-wrap"><h2>Resumen de indicadores</h2><table><thead><tr><th>Indicador</th><th>Resultado</th></tr></thead><tbody><tr><td>Total de encuestas</td><td>{{ $responses->count() }}</td></tr><tr><td>Experiencia global favorable (Muy Buena + Buena)</td><td>{{ $favorable }}%</td></tr><tr><td>Promedio limpieza y desinfección</td><td>{{ $cleaning['average'] ?? '—' }}</td></tr><tr><td>Promedio general de servicios</td><td>{{ $matrixStats['average'] ?? '—' }}</td></tr></tbody></table></div>

@if($suggestions->count())<div class="table-wrap"><h2>Sugerencias y recomendaciones</h2><table><thead><tr><th>Fecha</th><th>Observación</th></tr></thead><tbody>@foreach($suggestions as $s)<tr><td>{{ $s['date'] }}</td><td style="text-align:left">{{ $s['text'] }}</td></tr>@endforeach</tbody></table></div>@endif
</div></body></html>
