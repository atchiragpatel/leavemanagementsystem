<?php

namespace App\Http\Validators;

use App\Holiday;
use App\LeaveTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator as BaseValidator;

/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 8/2/17
 * Time: 10:46 AM
 */
class LeaveValidator
{
    public function handle($attribute, $value, $parameters, BaseValidator $validator)
    {
        $data = $validator->getData();
        $userId = Auth::user()->id;

        $fromDate = new Carbon($data['from_date']);
        $toDate = new Carbon($data['to_date']);
        $value = $toDate->diffInDays($fromDate);

        $legalLeave = LeaveTransaction::where('user_id', '=', $userId)
            ->where('leave_type', '=', $data['leave_type'])
            ->orderBy('id', 'desc')
            ->first()->ledger;

        return $value <= $legalLeave ? true : false;

    }

//    public function isSandwitch($attribute, $value, $parameters, BaseValidator $validator)
//    {
//        $data = $validator->getData();
//        $userId = Auth::user()->id;
//
//        /*
//         * This is for EVEN and ODD Saturday calculation
//         */
//        $evenSaturdayFirst = Carbon::parse('first saturday of this month');
//        $oddSaturdaySecond = Carbon::parse('second saturday of this month');
//        $evenSaturdayThird = Carbon::parse('third saturday of this month');
//        $oddSaturdayFourth = Carbon::parse('fourth saturday of this month');
//        $evenSaturdayFifth = Carbon::parse('fifth saturday of this month');
//
//        /*
//         * Calculate the total number of days (FROM_DATE to TO_DATE)
//         */
//        $fromDate = new Carbon($data['from_date']);
//        $toDate = new Carbon($data['to_date']);
//        $value = $toDate->diffInDays($fromDate);
//
//        /*
//         * To check any festival come before apply leave FROM_DATE
//         * Do Not Forgot to subtract 1 from the final value
//         */
//        $countFromDay = 0;
//        do {
//            $countFromDay++;
//            $fromDay = Holiday::where('festivaldate', 'like', $fromDate->subDay($countFromDay)->toDateString())->get();
//            $fromDate = new Carbon($data['from_date']);
//        } while ($fromDay->toArray());
//
//        /*
//         * To check any festival come after apply leave TO_DATE
//         * Do Not Forgot to subtract 1 from the final value
//         */
//        $countToDay = 0;
//        do {
//            $countToDay++;
//            $toDay = Holiday::where('festivaldate', 'like', $toDate->addDay($countToDay)->toDateString())->get();
//            $toDate = new Carbon($data['to_date']);
//        } while ($toDay->toArray());
//
//        /*
//         * To check IS Sat&Sun AND any Festival before leave apply FROM_DATE
//         */
//
//        if ($fromDate->subDay(1)->isSunday()) {
//            if ($fromDate->subDay(1) == $evenSaturdayFirst || $fromDate == $evenSaturdayThird || $fromDate == $evenSaturdayFifth) {
//                /*
//                 * Do Not Forgot to subtract 1 from the final value
//                 */
//                $fromSatSun = 2;
//                $temp = $fromSatSun;
//                do {
//                    $temp++;
//                    $tempFromSatSunQuery = Holiday::where('festivaldate', 'like', $fromDate->subDay($temp)->toDateString())->get();
//                    $fromDate = $data['from_date'];
//                } while ($tempFromSatSunQuery->toArray());
//                $fromSatSun = $temp - 1;
//            } else {
//                $fromSatSun = 1;
//            }
//        } else {
//            $fromSatSun = 0;
//        }
//
//        /*
//         * To check IS Sat&Sun AND any Festival after leave apply TO_DATE
//         */
//        if (($toDate->addDay(1)->isSaturday()) && ($toDate == $evenSaturdayFirst || $toDate == $evenSaturdayThird || $toDate == $evenSaturdayFifth)) {
//            /*
//             * Do Not Forgot to subtract 1 from the final value
//             */
//            $toSatSun = 2;
//            $temp = $toSatSun;
//            do {
//                $temp++;
//                $tempToSatSunQuery = Holiday::where('festivaldate', 'like', $toDate->addDay($temp)->toDateString())->get();
//                $toDate = $data['to_date'];
//            } while ($tempToSatSunQuery->toArray());
//            $toSatSun = $temp -1;
//        } else {
//            $toSatSun = 0;
//        }
//
//        $totalLeave = $value + ($countFromDay - 1) + ($countToDay - 1) + $fromSatSun + $toSatSun;
//
//        if ($totalLeave >= 4) {
//            dd($totalLeave);
//            return false;
//        } else {
//            return true;
//        }
//    }
}