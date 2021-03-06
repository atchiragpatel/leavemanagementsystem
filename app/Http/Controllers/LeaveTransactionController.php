<?php

namespace App\Http\Controllers;

use App\Holiday;
use App\Http\Requests\LeaveRequest;
use App\LeaveTransaction;
use App\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveTransactionController extends Controller
{
    public function applyForLeave()
    {
        $userId = Auth::user()->id;
        $leaves = LeaveTransaction::where('user_id', '=', $userId)
            ->get();
        if ($leaves->toArray()) {
            foreach ($leaves as $leave) {
                switch ($leave['leave_type']) {
                    case "SICK LEAVE":
                        $sickLeaveLedger = LeaveTransaction::where('leave_type', '=', 'SICK LEAVE')
                            ->orderBy('id', 'desc')
                            ->first()->ledger;
                        break;
                    case "CASUAL LEAVE":
                        $casualLeaveLedger = LeaveTransaction::where('leave_type', '=', 'CASUAL LEAVE')
                            ->orderBy('id', 'desc')
                            ->first()->ledger;
                        break;
                    case "PRIVILEGE LEAVE":
                        $privilegeLeaveLedger = LeaveTransaction::where('leave_type', '=', 'PRIVILEGE LEAVE')
                            ->orderBy('id', 'desc')
                            ->first()->ledger;
                        break;
                    default:
                        echo "";
                }
            }
        }

        return view('leave.applyforleave')
            ->with([
                'sickLeave' => $sickLeaveLedger,
                'casualleave' => $casualLeaveLedger,
                'privilegeleave' => $privilegeLeaveLedger
            ]);
    }

    public function submitLeave(LeaveRequest $request)
    {
        /*
         * Calculation for to_date to from_date
         * for total days.
         */
        $fromDate = new Carbon($request->from_date);
        $toDate = new Carbon($request->to_date);
        $value = $toDate->diffInDays($fromDate);

        /*
         * Leave request save to request table.
         */
        $leave = new \App\Request;
        $userId = Auth::user()->id;

        $leave->user_id = $userId;
        $leave->leave_type = $request->leave_type;
        $leave->txn_type = 'DEBIT';
        $leave->from_date = $request->from_date;
        $leave->to_date = $request->to_date;
        $leave->value = $value;
        $leave->status = 'REQUESTED';
        $leave->user_comments = $request->user_comments;

        $leave->save();

        return redirect('home');
    }

    /*
     * This function will fetch all the leave request with the status of REQUESTED
     * for ADMIN.
     */
    public function leaveRequest()
    {
        $leaveRequest = \App\Request::where('status', '=', 'REQUESTED')
            ->get();
        return view('employee.leaverequest')->with('leaverequest', $leaveRequest);
    }

    public function leaveApplied()
    {
        $userId = Auth::user()->id;
        $leaveApplied = Request::where('user_id', '=', $userId)
            ->get();
        return view('employee.appliedleaves')->with('leaveapplied', $leaveApplied);
    }

    public function leaveApprove($id)
    {
        $leave = \App\Request::find($id);

        $leave->status = 'APPROVED';
        $leave->save();

        return redirect('leaverequest');
    }

    public function leaveReject($id)
    {
        $leave = \App\Request::find($id);

        $leave->status = 'REJECTED';
        $leave->save();

        return redirect('leaverequest');
    }

    /*
     * This function is for leave calculation
     */
    public function isSandwitch()
    {
        $from_date = $_GET['from_date'];
        $to_date = $_GET['to_date'];
        $leave_type = $_GET['leave_type'];
        $sick_leave = $_GET['sick_leave'];
        $casual_leave = $_GET['casual_leave'];
        $privilege_leave = $_GET['privilege_leave'];

        $userId = Auth::user()->id;

        /*
         * This is for EVEN and ODD Saturday calculation
         */
        $fromMonth = Carbon::parse($from_date)->format('F');
        $toMonth = Carbon::parse($to_date)->format('F');

        $fromOddSaturdayFirst = Carbon::parse('first saturday of ' . $fromMonth);
        $fromEvenSaturdaySecond = Carbon::parse('second saturday of' . $fromMonth);
        $fromOddSaturdayThird = Carbon::parse('third saturday of ' . $fromMonth);
        $fromEvenSaturdayFourth = Carbon::parse('fourth saturday of ' . $fromMonth);
        $fromOddSaturdayFifth = Carbon::parse('fifth saturday of ' . $fromMonth);


        $toOddSaturdayFirst = Carbon::parse('first saturday of ' . $toMonth);
        $toOddSaturdayThird = Carbon::parse('third saturday of ' . $toMonth);
        $toOddSaturdayFifth = Carbon::parse('fifth saturday of ' . $toMonth);

        /*
         * This is for previous and next month EVEN and ODD Saturday calculation
         */
        $previousFromMonth = Carbon::parse($from_date)->subMonth(1)->format('F');
        $nextToMonth = Carbon::parse($to_date)->addMonth(1)->format('F');

        $fromPreviousMonthOddSaturdayFifth = Carbon::parse('fifth saturday of ' . $previousFromMonth);

        $toNextMonthOddSaturdayFirst = Carbon::parse('first saturday of ' . $nextToMonth);


        $fromSatSun = 0;
        $toSatSun = 0;
        $countHolidayFrom = 0;
        $countHolidayTo = 0;

        /*
         * Calculate the total number of days (FROM_DATE to TO_DATE)
         */
        $fromDate = new Carbon($from_date);
        $toDate = new Carbon($to_date);
        $value = $toDate->diffInDays($fromDate) + 1;

        /*
         * To check any festival come before apply leave FROM_DATE
         * Do Not Forgot to subtract 1 from the final value
         */
        $countFromDay = 0;
        do {
            $countFromDay++;
            $fromDay = Holiday::where('festivaldate', 'like', $fromDate->subDay($countFromDay)->toDateString())->get();
            $fromDate = new Carbon($from_date);
        } while ($fromDay->toArray());

        /*
         * To check any festival come after apply leave TO_DATE
         * Do Not Forgot to subtract 1 from the final value
         */
        $countToDay = 0;
        do {
            $countToDay++;
            $toDay = Holiday::where('festivaldate', 'like', $toDate->addDay($countToDay)->toDateString())->get();
            $toDate = new Carbon($to_date);
        } while ($toDay->toArray());

        /*----------------------------------- FOR FROM DATE -----------------------------------*/

        /*
         * Below calculation is for $fromDate.
         * To check any Festival IS coming before $fromDate.
         */

        /*
         * $tempFromDate is the date which include sandwitch days.
         */
        $tempFromDate = new Carbon($from_date);

        /*
         * To check if from day before one day is not sunday,
         * not odd saturday,
         * but is holiday.
         */
        $fromHoliday = Holiday::where('festivaldate', 'like', $fromDate->subDay(1)->toDateString())->get();
        if (!$fromHoliday->toArray()) {
            $fromDate = new Carbon($from_date);
        }
        /*
         * To check ($fromdate - 1) is holiday
         */
        if ($fromHoliday->toArray()) {
            $countHolidayFrom = 0;
            do {
                $countHolidayFrom++;
                $tempFromHolidayQuery = Holiday::where('festivaldate', 'like', $fromDate->subDay($countHolidayFrom)->toDateString())->get();
                $tempFromDate = $fromDate->toDateString();
                $fromDate = new Carbon($from_date);
            } while ($tempFromHolidayQuery->toArray());
            $countHolidayFrom--;
        } /*
         * To check $fromdate is sunday
         */
        elseif ($fromDate->subDay(1)->isSunday()) {
            /*
             * To check ($fromdate - 1) is odd saturday
             */
            if ($fromDate->subDay(1) == $fromOddSaturdayFirst || $fromDate == $fromOddSaturdayThird || $fromDate == $fromOddSaturdayFifth || $fromDate == $fromPreviousMonthOddSaturdayFifth) {
                /*
                 * Do Not Forgot to subtract 1 from the final value
                 */
                $fromSatSun = 2; //here i change the value from 1 to 2 for testing
                //$temp = $fromSatSun;
                $temp = 0;
                do {
                    $temp++;
                    $tempFromSatSunQuery = Holiday::where('festivaldate', 'like', $fromDate->subDay($temp)->toDateString())->get();
                    $tempFromDate = $fromDate->toDateString();
                    $fromDate = new Carbon($from_date);
                } while ($tempFromSatSunQuery->toArray());
                $fromSatSun = $fromSatSun + ($temp - 1);
            } else {
                $fromSatSun = 0;
            }
        } /*
         * To check $fromdate is odd saturday
         */
        elseif ($fromDate == $fromOddSaturdayFirst || $fromDate == $fromOddSaturdayThird || $fromDate == $fromOddSaturdayFifth) {
            $fromSatSun = 1;
            //$temp = $fromSatSun;
            $temp = 0;
            do {
                $temp++;
                $tempFromSatSunQuery = Holiday::where('festivaldate', 'like', $fromDate->subDay($temp)->toDateString())->get();
                $tempFromDate = $fromDate->toDateString();
                $fromDate = new Carbon($from_date);

            } while ($tempFromSatSunQuery->toArray());
            $fromSatSun = $fromSatSun + ($temp - 1);
        } else {
            $fromSatSun = 0;
            $tempFromDate->subDay(1);
        }
        $newTempFromDate = new Carbon($tempFromDate);
        $newTempFromDate->addDay(1);

        /*----------------------------------- FOR TO DATE -----------------------------------*/

        /*
         * Below calculation is for $todate
         * To check Is Sat&Sun AND any Festival come after leave apply
         */

        /*
         * $tempToDate is the date which include sandwitch days
         */

        $tempToDate = new Carbon($to_date);
        /*
         * To check if $todate after one day is not sunday
         * not odd saturday
         * but is holiday
         */
        $toHoliday = Holiday::where('festivaldate', 'like', $toDate->addDay(1)->toDateString())->get();
        if (!$toHoliday->toArray()) {
            $toDate = new Carbon($to_date);
        }
        /*
         * To check ($todate + 1) is holiday
         */
        if ($toHoliday->toArray()) {
            $countHolidayTo = 0;
            do {
                $countHolidayTo++;
                $tempToHolidayQuery = Holiday::where('festivaldate', 'like', $fromDate->addDay($countHolidayTo)->toDateString())->get();
                $tempToDate = $toDate->toDateString();
                $toDate = new Carbon($to_date);
            } while ($tempToHolidayQuery->toArray());
            $countHolidayTo--;
        } /*
         * To check $todate is saturday
         */
        elseif (($toDate->addDay(1)->isSaturday()) && ($toDate == $toOddSaturdayFirst || $toDate == $toOddSaturdayThird || $toDate == $toOddSaturdayFifth || $toDate == $toNextMonthOddSaturdayFirst)) {
            /*
             * Do Not Forgot to subtract 1 from the final value
             */
            $toSatSun = 2;
            //$temp = $toSatSun;
            $temp = 0;
            do {
                $temp++;
                $tempToSatSunQuery = Holiday::where('festivaldate', 'like', $toDate->addDay($temp)->toDateString())->get();
                $tempToDate = $toDate->toDateString();
                $toDate = new Carbon($to_date);
            } while ($tempToSatSunQuery->toArray());
            $toSatSun = $toSatSun + ($temp - 1);
        } /*
         * To check ($todate - 1) is sunday
         */
        else if ($toDate->isSunday()) {
            $toSatSun = 1;
            //$temp = $toSatSun;
            $temp = 0;
            do {
                $temp++;
                $tempToSatSunQuery = Holiday::where('festivaldate', 'like', $toDate->addDay($temp)->toDateString())->get();
                $tempToDate = $toDate->subDay(1)->toDateString();
                $toDate = new Carbon($to_date);
            } while ($tempToSatSunQuery->toArray());
            $toSatSun = $toSatSun + ($temp - 1);
        } else {
            $toSatSun = 0;
        }
        /*
         * To check is any even saturdays come in between $newTempFromDate and $tempToDate
         */

        $start = new Carbon($newTempFromDate);
        $end = new Carbon($tempToDate);
        $count = 0;
        $evenSat = 0;

        for ($i = $start; $i <= $end; $i->addDay(1)) {
            $count++;
            if (($start == $fromEvenSaturdaySecond) || ($start == $fromEvenSaturdayFourth)) {
                $evenSat++;
            }
            $newTempFromDate->addDay(1);
        }
        /*
         * This is the total leave including Value(official leave fromdate + todate)
         */
        $totalLeave = $value + ($countFromDay - 1) + ($countToDay - 1) + $fromSatSun + $toSatSun + ($countHolidayFrom) + ($countHolidayTo) + $evenSat;

        /*
         * To check total leave is greater than applied leave
         */
        if ($totalLeave >= 4) {

            $sandwitchLeave = $totalLeave > $value ? ($totalLeave - $value) : 0;
            $returnData = [];
            switch ($leave_type) {
                case ("SICK LEAVE"):
                    if ($value <= $sick_leave) {
                        $returnData[] = ["Deduct from sick leave =>" . ' ' . $value];
//                        $returnData[] = [
//                            "Type" => "SICK LEAVE",
//                            "From Date" => $from_date,
//                            "To Date" => $to_date,
//                            "days" => $value,
//                            "Note" => " ",
//                        ];
                        if ($sandwitchLeave <= $privilege_leave) {
                            $returnData[] = "Deduct from privilege leave =>" . ' ' . $sandwitchLeave;
//                            $returnData[] = [
//                                "Type" => "PRIVILEGE LEAVE",
//                                "From Date" => $from_date,
//                                "To Date" => $to_date,
//                                "days" => $sandwitchLeave,
//                                "Note" => "SANDWITCH",
//                            ];
                        } else {
                            $remainingLeave = $sandwitchLeave - $privilege_leave;
                            $returnData[] = [
                                "Deduct from privilege leave =>" . ' ' . $privilege_leave,
                                "Deduct from leave without pay =>" . ' ' . $remainingLeave
                            ];
//                            $returnData[] = [
//                                "Type" => "PRIVILEGE LEAVE",
//                                "From Date" => $from_date,
//                                "To Date" => $to_date,
//                                "days" => $privilege_leave,
//                                "Note" => "SANDWITCH",
//                            ];
//                            $returnData[] = [
//                                "Type" => "LEAVE WITHOUT PAY",
//                                "From Date" => $from_date,
//                                "To Date" => $to_date,
//                                "days" => $remainingLeave,
//                                "Note" => "SANDWITCH",
//                            ];
                        }
                    } elseif ($value > $sick_leave) {
                        $sickSandwitch = $value - $sick_leave;
                        $returnData[] = "Deduct from sick leave =>" . ' ' . $sick_leave;
//                        $returnData[] = [
//                            "Type" => "SICK LEAVE",
//                            "From Date" => $from_date,
//                            "To Date" => $to_date,
//                            "days" => $sick_leave,
//                            "Note" => " ",
//                        ];
                        if (($sandwitchLeave + $sickSandwitch) <= $privilege_leave) {
                            $returnData[] = "Deduct from privilege leave =>" . ' ' . ($sandwitchLeave + $sickSandwitch);
//                            $returnData[] = [
//                                "Type" => "PRIVILEGE LEAVE",
//                                "From Date" => $from_date,
//                                "To Date" => $to_date,
//                                "days" => ($sandwitchLeave + $sickSandwitch),
//                                "Note" => "SANDWITCH",
//                            ];
                        } else {
                            $remainingLeave = ($sandwitchLeave + $sickSandwitch) - $privilege_leave;
                            $returnData[] = [
                                "Deduct from privilege leave =>" . ' ' . $privilege_leave,
                                "Deduct from leave without pay =>" . ' ' . $remainingLeave
                            ];
//                            $returnData[] = [
//                                "Type" => "PRIVILEGE LEAVE",
//                                "From Date" => $from_date,
//                                "To Date" => $to_date,
//                                "days" => $privilege_leave,
//                                "Note" => "SANDWITCH",
//                            ];
//                            $returnData[] = [
//                                "Type" => "LEAVE WITHOUT PAY",
//                                "From Date" => $from_date,
//                                "To Date" => $to_date,
//                                "days" => $remainingLeave,
//                                "Note" => " ",
//                            ];
                        }
                    }
//                    return json_encode($returnData);
                    return $returnData;
                    break;
                case ("CASUAL LEAVE"):
                    if ($value <= $casual_leave) {
                        $returnData[] = "Deduct from casual leave =>" . ' ' . $value;
                        if ($sandwitchLeave <= $privilege_leave) {
                            $returnData[] = "Deduct from privilege leave =>" . ' ' . $sandwitchLeave;
                        } else {
                            $remainingLeave = $sandwitchLeave - $privilege_leave;
                            $returnData[] = [
                                "Deduct from privilege leave =>" . ' ' . $privilege_leave,
                                "Deduct from leave without pay =>" . ' ' . $remainingLeave
                            ];
                        }
                    } elseif ($value > $casual_leave) {
                        $casualSandwitch = $value - $casual_leave;
                        $returnData[] = "Deduct from casual leave =>" . ' ' . $casual_leave;
                        if (($sandwitchLeave + $casualSandwitch) <= $privilege_leave) {
                            $returnData[] = "Deduct from privilege leave =>" . ' ' . ($sandwitchLeave + $casualSandwitch);
                        } else {
                            $remainingLeave = ($sandwitchLeave + $casualSandwitch) - $privilege_leave;
                            $returnData[] = [
                                "Deduct from privilege leave =>" . ' ' . $privilege_leave,
                                "Deduct from leave without pay =>" . ' ' . $remainingLeave
                            ];
                        }
                    }
                    return $returnData;
                    break;
                case ("PRIVILEGE LEAVE"):
                    if ($totalLeave <= $privilege_leave) {
                        $returnData[] = "Deduct from privilege leave =>" . ' ' . $value;
                    } else {
                        $remainingLeave = $totalLeave - $privilege_leave;
                        $returnData[] = [
                            "Deduct from privilege leave =>" . ' ' . $privilege_leave,
                            "Deduct from leave without pay =>" . ' ' . $remainingLeave
                        ];
                    }
                    return $returnData;
                    break;
                case ("LEAVE WITHOUT PAY"):
                    return [
                        "Deduct from leave without pay =>" . ' ' . $totalLeave
                    ];
                    break;
                default :
                    return "Please select leave type";
            }
        }
        return "Deduct from $leave_type => $value";
    }
}
