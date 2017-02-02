<?php

namespace App\Http\Controllers;

use App\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function listHolidays()
    {
        $holidays = Holiday::all();
        return view('holidays.listholidays')->with('holidays',$holidays);
    }

    public function addHolidays()
    {
        return view('holidays.addholidays');
    }

    public function holidayDetails(Request $request)
    {
        $holiday = new Holiday;

        $holiday->festivalname = $request->festivalname;
        $holiday->festivaldate = $request->festivaldate;

        $holiday->save();

        return redirect('listholidays');
    }

    public function editHoliday($id)
    {
        $holiday = Holiday::find($id);
        return view('holidays.editholiday')->with('holiday',$holiday);
    }

    public function storeEditHoliday($id, Request $request)
    {
        $holidayData = Holiday::find($id);

        $holidayData->festivalname = $request->festivalname;
        $holidayData->festivaldate = $request->festivaldate;

        $holidayData->save();

        return redirect('listholidays');

    }

    public function deleteHoliday($id)
    {
        $holiday = Holiday::find($id);
        $holiday->delete();

        return redirect('listholidays');
    }
}
