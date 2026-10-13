<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Informes estadísticos</title>
<style>
body{margin:0;font-family:Arial,sans-serif;background:#f3f6f8;color:#1f2937}.wrap{max-width:1200px;margin:25px auto;padding:0 18px}.header,.card{background:#fff;border-radius:14px;padding:22px;margin-bottom:18px;box-shadow:0 5px 18px rgba(0,0,0,.06)}h1{margin:0;color:#0f4c5c}h2{color:#0f4c5c;margin-top:0}.muted{color:#64748b}.filters{display:grid;grid-template-columns:1fr 2fr auto;gap:14px;align-items:end}.field label{display:block;font-weight:700;margin-bottom:6px}.field select,.btn{width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff}.btn{background:#0f4c5c;color:#fff;font-weight:700;text-decoration:none;display:inline-block;text-align:center;cursor:pointer}.btn.secondary{background:#19728a}.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}.actions a{width:auto}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.stat{padding:18px;border:1px solid #e2e8f0;border-radius:10px}.stat b{display:block;font-size:27px;color:#0f4c5c;margin-top:5px}.table-wrap{overflow:auto}table{border-collapse:collapse;width:100%}th,td{border:1px solid #dbe2e8;padding:9px;text-align:left}th{background:#eaf2f5}.bar{height:12px;background:#dbe7eb;border-radius:10px;overflow:hidden}.bar span{display:block;height:100%;background:#0f4c5c}.question{margin-bottom:24px}.avg{font-weight:700;color:#0f4c5c}@media(max-width:800px){.filters,.stats{grid-template-columns:1fr}.actions a{width:100%}}
</style></head><body>
<div class="wrap">
<div class="header"><h1>Informes estadísticos</h1><p class="muted">ENCUESTA DE SATISFACCIÓN DEL USUARIO — {{ $periodLabel }}</p></div>
<div class="card">
<form method="GET" action="{{ route('admin.reports.index') }}">
<div class="filters">
<div class="field"><label>Año</label><select name="year">@foreach($years as $y)<option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>@endforeach</select></div>
<div class="field"><label>Trimestre</label><select name="quarter_id"><option value="">Todos los trimestres del año</option>@foreach($quarters->where('year',$year) as $q)<option value="{{ $q->id }}" @selected($selectedQuarter?->id===$q->id)>{{ $q->name }} ({{ $q->start_date->format('d/m/Y') }} - {{ $q->end_date->format('d/m/Y') }})</option>@endforeach</select></div>
<button class="btn" type="submit">Consultar</button>
</div>
</form>
<div class="actions"><a class="btn secondary" href="{{ route('admin.reports.matrix', request()->query()) }}">Ver matriz estadística</a><a class="btn" href="{{ route('admin.reports.pdf', request()->query()) }}">Descargar PDF completo</a><a class="btn" href="{{ route('home') }}">← Regresar al Dashboard</a></div>
</div>
<div class="card"><h2>Resumen</h2><div class="stats">
<div class="stat">Encuestas respondidas<b>{{ $responses->count() }}</b></div>
<div class="stat">Experiencia favorable<b>{{ $favorable }}%</b></div>
<div class="stat">Promedio limpieza<b>{{ $cleaning['average'] ?? '—' }}</b></div>
<div class="stat">Promedio servicios<b>{{ $matrixStats['average'] ?? '—' }}</b></div>
</div></div>

@foreach($questionStats as $stat)
<div class="card question"><h2>{{ $stat['question']->position }}. {{ $stat['question']->question_text }}</h2>
@if($stat['question']->type === 'matrix')
<div class="table-wrap"><table><thead><tr><th>Servicio</th><th>Total</th><th>Promedio</th></tr></thead><tbody>@foreach($stat['items'] as $item)<tr><td>{{ $item['label'] }}</td><td>{{ $item['total'] }}</td><td class="avg">{{ $item['average'] ?? '—' }}</td></tr>@endforeach</tbody></table></div>
@elseif(isset($stat['options']))
<div class="table-wrap"><table><thead><tr><th>Respuesta</th><th>Cantidad</th><th>%</th><th>Distribución</th></tr></thead><tbody>@foreach($stat['options'] as $option)<tr><td>{{ $option['label'] }}</td><td>{{ $option['count'] }}</td><td>{{ $stat['total'] ? round($option['count']/$stat['total']*100,1) : 0 }}%</td><td><div class="bar"><span style="width:{{ $stat['total'] ? ($option['count']/$stat['total']*100) : 0 }}%"></span></div></td></tr>@endforeach</tbody></table></div>
@endif</div>
@endforeach

@if($suggestions->count())<div class="card"><h2>Sugerencias y recomendaciones</h2><div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Observación</th></tr></thead><tbody>@foreach($suggestions as $s)<tr><td>{{ $s['date'] }}</td><td>{{ $s['text'] }}</td></tr>@endforeach</tbody></table></div></div>@endif
</div></body></html>
