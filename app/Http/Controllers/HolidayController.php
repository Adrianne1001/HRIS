<?php

namespace App\Http\Controllers;

use App\Enums\HolidayType;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $holidays = Holiday::where('year', $year)
            ->orderBy('date')
            ->paginate(20)
            ->withQueryString();

        $years = Holiday::selectRaw('DISTINCT year')->orderBy('year', 'desc')->pluck('year');

        return view('holidays.index', [
            'holidays' => $holidays,
            'currentYear' => $year,
            'years' => $years,
            'holidayTypes' => HolidayType::cases(),
        ]);
    }

    public function create()
    {
        return view('holidays.create', [
            'holidayTypes' => HolidayType::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'holidayType' => ['required', 'in:Regular Holiday,Special Non-Working Day'],
            'year' => ['required', 'integer', 'min:2020'],
        ]);

        Holiday::create($validated);

        return redirect()->route('holidays.index')->with('success', 'Holiday created successfully.');
    }

    public function edit(Holiday $holiday)
    {
        return view('holidays.edit', [
            'holiday' => $holiday,
            'holidayTypes' => HolidayType::cases(),
        ]);
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'holidayType' => ['required', 'in:Regular Holiday,Special Non-Working Day'],
            'year' => ['required', 'integer', 'min:2020'],
        ]);

        $holiday->update($validated);

        return redirect()->route('holidays.index')->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('holidays.index')->with('success', 'Holiday deleted successfully.');
    }
}
