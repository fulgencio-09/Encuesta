<?php

namespace App\Http\Controllers;

use App\Models\ReportingQuarter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuarterController extends Controller
{
    public function index()
    {
        $quarters = ReportingQuarter::orderByDesc('year')->orderBy('quarter')->get();
        return view('admin.quarters.index', compact('quarters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'quarter' => [
                'required',
                'integer',
                Rule::in([1, 2, 3, 4]),
                Rule::unique('reporting_quarters')->where(fn ($query) => $query->where('year', $request->integer('year'))),
            ],
        ], [
            'quarter.unique' => 'Ese trimestre ya existe para el año seleccionado.',
        ]);

        $ranges = [
            1 => ['01-01', '03-31', 'Primer Trimestre'],
            2 => ['04-01', '06-30', 'Segundo Trimestre'],
            3 => ['07-01', '09-30', 'Tercer Trimestre'],
            4 => ['10-01', '12-31', 'Cuarto Trimestre'],
        ];

        [$start, $end, $label] = $ranges[$data['quarter']];

        DB::transaction(function () use ($data, $start, $end, $label) {
            // Solo puede existir un trimestre activo para determinar qué encuesta está disponible.
            ReportingQuarter::where('is_active', true)->update(['is_active' => false]);

            ReportingQuarter::create([
                'year' => $data['year'],
                'quarter' => $data['quarter'],
                'name' => $label . ' ' . $data['year'],
                'start_date' => $data['year'] . '-' . $start,
                'end_date' => $data['year'] . '-' . $end,
                'is_active' => true,
            ]);
        });

        return back()->with('success', 'Trimestre creado correctamente y establecido como trimestre activo.');
    }

    public function toggle(ReportingQuarter $quarter)
    {
        if ($quarter->is_active) {
            $quarter->update(['is_active' => false]);
            return back()->with('success', 'Trimestre desactivado. La encuesta pública queda temporalmente no disponible.');
        }

        DB::transaction(function () use ($quarter) {
            ReportingQuarter::where('is_active', true)->update(['is_active' => false]);
            $quarter->update(['is_active' => true]);
        });

        return back()->with('success', 'Trimestre activado. La encuesta pública queda asociada a este período.');
    }
}
