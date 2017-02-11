<?php

namespace App\Http\Controllers;

use App\Holiday;
use App\Http\Requests\LeaveRequest;
use App\LeaveTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
                        echo "No leaves remaining for you";
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
        dd('chirag');
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
        $oddSaturdayFirst = Carbon::parse('first saturday of this month');
        $evenSaturdaySecond = Carbon::parse('second saturday of this month');
        $oddSaturdayThird = Carbon::parse('third saturday of this month');
        $evenSaturdayFourth = Carbon::parse('fourth saturday of this month');
        $oddSaturdayFifth = Carbon::parse('fifth saturday of this month');

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

        /*
         * To check IS Sat&Sun AND any Festival before leave apply FROM_DATE
         */

        $tempFromDate = new Carbon($from_date);
        if ($fromDate->subDay(1)->isSunday()) {
            if ($fromDate->subDay(1) == $oddSaturdayFirst || $fromDate == $oddSaturdayThird || $fromDate == $oddSaturdayFifth) {
                /*
                 * Do Not Forgot to subtract 1 from the final value
                 */
                $fromSatSun = 2;
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
                $fromSatSun = 1;
            }
        } elseif ($fromDate == $oddSaturdayFirst || $fromDate == $oddSaturdayThird || $fromDate == $oddSaturdayFifth) {
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
        }
        /*
         * To check IS Sat&Sun AND any Festival after leave apply TO_DATE
         */

        $tempToDate = new Carbon($to_date);
        if (($toDate->addDay(1)->isSaturday()) && ($toDate == $oddSaturdayFirst || $toDate == $oddSaturdayThird || $toDate == $oddSaturdayFifth)) {
            /*
             * Do Not Forgot to subtract 1 from the final value
             */
            $toSatSun = 2;
            //$temp = $toSatSun;
            $temp = 0;
            do {
                $temp++;
                $tempToSatSunQuery = Holiday::where('festivaldate', 'like', $toDate->addDay($temp)->toDateString())->get();
                $tempToDate = $fromDate->addDay($temp)->toDateString();
                $toDate = new Carbon($to_date);
            } while ($tempToSatSunQuery->toArray());
            $toSatSun = $toSatSun + ($temp - 1);
        } else if ($toDate->isSunday()) {
            $toSatSun = 1;
            //$temp = $toSatSun;
            $temp = 0;
            do {
                $temp++;
                $tempToSatSunQuery = Holiday::where('festivaldate', 'like', $toDate->addDay($temp)->toDateString())->get();
                $tempToDate = $toDate->toDateString();
                $toDate = new Carbon($to_date);
            } while ($tempToSatSunQuery->toArray());
            $toSatSun = $toSatSun + ($temp - 1);
            return $toSatSun;
        } else {
            $toSatSun = 0;
        }

        $totalLeave = $value + ($countFromDay - 1) + ($countToDay - 1) + $fromSatSun + $toSatSun;

        /*
         * To check total leave is greater than applied leave
         */
        if ($totalLeave >= 4) {

            $chiragTemp = $totalLeave > $value ? ($totalLeave - $value) : 0;
            switch ($leave_type) {
                case ("SICK LEAVE"):
                    if ($sick_leave >= $totalLeave) {
                        return [
                            "Apply for =>". ' ' . $value,
                            "Total Leave count =>". ' ' . $totalLeave,
                            "Deduct from sick leave =>" . ' ' . $totalLeave,
                        ];
                    } else {
                        $temp = $totalLeave - $sick_leave;
                        if ($privilege_leave >= $temp) {
                            return [
                                "Apply for =>". ' ' . $value,
                                "Total Leave count =>". ' ' . $totalLeave,
                                "Deduct from sick leave =>" . ' ' . $sick_leave,
                                "Deduct from privilege leave =>" . ' ' . $temp
                            ];
                        } else {
                            $temp1 = $temp - $privilege_leave;
                            return [
                                "Apply for =>". ' ' . $value,
                                "Total Leave count =>". ' ' . $totalLeave,
                                "Deduct from sick leave =>" . ' ' . $sick_leave,
                                "Deduct from privilege leave =>" . ' ' . $privilege_leave,
                                "Deduct from leave without pay =>" . ' ' . $temp1
                            ];
                        }
                    }
                    break;
                case ("CASUAL LEAVE"):
                    if ($casual_leave >= $totalLeave) {
                        return [
                            "Apply for =>". ' ' . $value,
                            "Total Leave count =>". ' ' . $totalLeave,
                            "Deduct from casual leave =>" . ' ' . $totalLeave,
                        ];
                    } else {
                        $temp = $totalLeave - $casual_leave;
                        if ($privilege_leave >= $temp) {
                            return [
                                "Apply for =>". ' ' . $value,
                                "Total Leave count =>". ' ' . $totalLeave,
                                "Deduct from casual leave =>" . ' ' . $casual_leave,
                                "Deduct from privilege leave =>" . ' ' . $temp
                            ];
                        } else {
                            $temp1 = $temp - $privilege_leave;
                            return [
                                "Apply for =>". ' ' . $value,
                                "Total Leave count =>". ' ' . $totalLeave,
                                "Deduct from casual leave =>" . ' ' . $casual_leave,
                                "Deduct from privilege leave =>" . ' ' . $privilege_leave,
                                "Deduct from leave without pay =>" . ' ' . $temp1
                            ];
                        }
                    }
                    break;
                case ("PRIVILEGE LEAVE"):
                    if ($privilege_leave >= $totalLeave) {
                        return [
                            "Apply for =>". ' ' . $value,
                            "Total Leave count =>". ' ' . $totalLeave,
                            "Deduct from privilege leave =>" . ' ' . $totalLeave,
                        ];
                    } else {
                        $temp = $totalLeave - $privilege_leave;
                        return [
                            "Apply for =>". ' ' . $value,
                            "Total Leave count =>". ' ' . $totalLeave,
                            "Deduct from privilege leave =>" . ' ' . $privilege_leave,
                            "Deduct from leave without pay =>" . ' ' . $temp
                        ];
                    }
                    break;
                case ("LEAVE WITHOUT PAY"):
                    return [
                        "Apply for =>". ' ' . $value,
                        "Total Leave count =>". ' ' . $totalLeave,
                        "Deduct from leave without pay =>" . ' ' . $totalLeave,
                    ];
                    break;
                default :
                    return "Please select leave type";
            }
        }
        return "Tu bachi gayo";
        //return $temp;
    }
}
