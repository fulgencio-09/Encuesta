<?php

namespace App\Http\Controllers;

use App\Models\ReportingQuarter;
use Illuminate\Http\Request;
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
            'quarter' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
        ]);

        $ranges = [
            1 => ['01-01', '03-31', 'Primer Trimestre'],
            2 => ['04-01', '06-30', 'Segundo Trimestre'],
            3 => ['07-01', '09-30', 'Tercer Trimestre'],
            4 => ['10-01', '12-31', 'Cuarto Trimestre'],
        ];

        [$start, $end, $label] = $ranges[$data['quarter']];

        ReportingQuarter::create([
            'year' => $data['year'],
            'quarter' => $data['quarter'],
            'name' => $label . ' ' . $data['year'],
            'start_date' => $data['year'] . '-' . $start,
            'end_date' => $data['year'] . '-' . $end,
            'is_active' => true,
        ]);

        return back()->with('success', 'Trimestre creado correctamente.');
    }

    public function toggle(ReportingQuarter $quarter)
    {
        $quarter->update(['is_active' => !$quarter->is_active]);
        return back()->with('success', $quarter->is_active ? 'Trimestre activado.' : 'Trimestre desactivado.');
    }
}
