<?php

namespace App\Console\Commands;

use App\Holiday;
use App\Request;
use App\User;
use App\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class LeaveTransaction extends Command
{
    const dayStart = "9:30";
    const dayEnd = "18:30";
    const timezone = "Asia/Kolkata";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atyantik:leave-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To credit leaves in leave transaction table';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->calcUserUninformedAbsence();
        // Get the last date that has status PENDING
        // Create new date with that date time
        // Loop through the date by adding 1 more day and calculate the leaves accordingly
        // till current date is reached
        $lastProcessedDate = Attendance::where("status", '=', 'PENDING')
            ->orderBy('created_at', 'ASC')
            ->limit(1)
            ->first();
        if (!$lastProcessedDate) {
            return;
        }
        $lastProcessedDate = new Carbon($lastProcessedDate->action_time);
        //$lastProcessedDate = new Carbon('2 feb 2017');
        $currentDate = Carbon::now();

        // Process all the entries from that date on to current day
        while ($lastProcessedDate < $currentDate) {
            $arr = [];

            $dayStart = new Carbon($lastProcessedDate->format("Y-m-d") . " 00:00:00", self::timezone);
            $dayStart->tz('UTC');

            $dayEnd = new Carbon($lastProcessedDate->format("Y-m-d") . " 23:59:59", self::timezone);
            $dayEnd->tz('UTC');

            $officialDayStart = new Carbon($lastProcessedDate->format("Y-m-d") . " " . self::dayStart, self::timezone);
            $officialDayStart->tz('UTC');

            $officialDayEnd = new Carbon($lastProcessedDate->format("Y-m-d") . " " . self::dayEnd, self::timezone);
            $officialDayEnd->tz('UTC');

            $officialDayStartWithBuffer = new Carbon($officialDayStart);
            $officialDayStartWithBuffer->addMinutes(30);

            $officialDayEndWithBuffer = new Carbon($officialDayEnd);
            $officialDayEndWithBuffer->subMinutes(30);
//            dd([
//                $dayStart,
//                $officialDayStart,
//                $officialDayStartWithBuffer,
//                $officialDayEnd,
//                $officialDayEndWithBuffer,
//                $dayEnd
//            ]);

            // Get user details attendance details for particular day
            $query = Attendance::whereIn('action_type', ['LOGIN', 'LOGOUT'])
                ->where('action_time', '>=', $dayStart)
                ->where('action_time', '<=', $dayEnd)
                ->where('status', '=', "PENDING")
                ->orderBy('user_id', 'asc')
                ->orderBy('action_time', 'asc');
            $loginDetails = $query->get();

            $loginDetails->each(function ($details) use (&$arr) {
                $details = $details->toArray();

                if (!isset($arr[$details["user_id"]])) {
                    if ($details["action_type"] == "LOGOUT") {
                        return;
                    }
                    $arr[$details["user_id"]] = [];
                }

                $lastDetails = (@array_slice($arr[$details["user_id"]], -1)[0]);
                if ($lastDetails && $lastDetails["action_type"] == $details["action_type"]) {
                    $key = key(array_slice($arr[$details["user_id"]], 0, 1, true));

                    if ($details["action_type"] == "LOGIN") {
                        $arr[$details["user_id"]][$key] = $details;
                    }
                    if ($details["action_type"] == "LOGOUT") {
                        $arr[$details["user_id"]][$key] = $lastDetails;
                    }
                } else {
                    $arr[$details["user_id"]][] = $details;
                }
            });

            foreach ($arr as $userId => $userAttendance) {
                $lastDetails = array_slice($userAttendance, -1)[0];
                if ($lastDetails["action_type"] == "LOGIN") {
                    $key = key(array_slice($userAttendance, -1, 1, true));
                    unset($arr[$userId][$key]);
                }
            }

            foreach ($arr as $userId => &$userAttendance) {
                $userAttendance = array_values($userAttendance);
            }

            foreach ($arr as $userId => $userAttendance) {

                $hasLoginBeforeOfficialStart = !!count(array_filter($userAttendance, function ($attendance) use ($officialDayStartWithBuffer) {
                    return $attendance["action_type"] == "LOGIN" && new Carbon($attendance["action_time"]) <= $officialDayStartWithBuffer;
                }));
                $hasLoginAfterOfficialStart = !!count(array_filter($userAttendance, function ($attendance) use ($officialDayStartWithBuffer) {
                    return $attendance["action_type"] == "LOGIN" && new Carbon($attendance["action_time"]) > $officialDayStartWithBuffer;
                }));

                $hasLogoutAfterOfficialEnd = !!count(array_filter($userAttendance, function ($attendance) use ($officialDayEndWithBuffer) {
                    return $attendance["action_type"] == "LOGOUT" && new Carbon($attendance["action_time"]) >= $officialDayEndWithBuffer;
                }));
                $hasLogoutBeforeOfficialEnd = !!count(array_filter($userAttendance, function ($attendance) use ($officialDayEndWithBuffer) {
                    return $attendance["action_type"] == "LOGOUT" && new Carbon($attendance["action_time"]) < $officialDayEndWithBuffer;
                }));

                $timespent = 0;
                $login = [];
                for ($i = 0; $i < count($userAttendance); $i = $i + 2) {
                    $login = $userAttendance[$i];
                    $logout = $userAttendance[$i + 1];
                    if ($login["action_type"] !== "LOGIN" || $logout["action_type"] !== "LOGOUT") {
                        die("An error has occurred, invalid array for user attendance");
                    }
                    $loginDateTime = Carbon::parse($login['action_time']);
                    $logoutDateTime = Carbon::parse($logout['action_time']);
                    $timespent = $logoutDateTime->diffInMinutes($loginDateTime);
                }

                $isFullDay = $hasLoginBeforeOfficialStart && $hasLogoutAfterOfficialEnd && $timespent > 480;
                $isHalfDay = !$isFullDay && $timespent > 240;
                $isFirstHalf = !$isFullDay && $hasLoginBeforeOfficialStart && $hasLogoutBeforeOfficialEnd;
                $isSecondHalf = !$isFullDay && $hasLoginAfterOfficialStart && $hasLogoutAfterOfficialEnd;

                /*
                 * If user physically present on particular day than
                 * add 0.0714 to leave transaction table. (calculate PL = 1/14)
                 * No matter $isFullDay, $isFirstHalf, $isSecondHalf, $isHalfDay
                 */

                $privilegeObject = new \App\LeaveTransaction();
                $privilegeData = \App\LeaveTransaction::where('user_id', '=', $login['user_id'])
                    ->where('leave_type', '=', 'PRIVILEGE LEAVE')
                    ->orderBy('id', 'desc')
                    ->first();
                if ($privilegeData) {
                    $privilegeLedger = $privilegeData['ledger'];
                } else {
                    $privilegeLedger = 0;
                }
                $privilegeObject->user_id = $login['user_id'];
                $privilegeObject->leave_type = 'PRIVILEGE LEAVE';
                $privilegeObject->value = 0.0714;
                $privilegeObject->type = 'CREDIT';
                $privilegeObject->ledger = $privilegeObject->value + $privilegeLedger;

                $privilegeObject->save();

                /*
                 * After processed PENDING entries the status of that user entry into
                 * Attendance table should be change to APPROVED.
                 */

                $attendanceObject = Attendance::where('user_id', '=', $login['user_id'])
                    ->get();
                foreach ($attendanceObject as $attendance) {
                    $attendance->status = "APPROVED";
                    $attendance->save();
                }
            }
            $lastProcessedDate = $lastProcessedDate->addDay(1);
        }
        //dd($arr);
    }

    /*
     * If User Joining Date is equal to 3 months than
     * add +5 casual leave as well as +5 sick leave
     */

    public function sickCasualLeaves()
    {
        $users = User::where('doj', '=', Carbon::now()->subMonth(+3)->toDateString())
            ->get();

        foreach ($users as $user) {
            /*
             * Sick Leave
             */
            $sickObject = new \App\LeaveTransaction();
            $sickLeaveData = \App\LeaveTransaction::where('user_id', '=', $user->id)
                ->where('leave_type', '=', 'SICK LEAVE')
                ->get();
            if ($sickLeaveData->toArray()) {
                $sickLeaveLedger = $sickLeaveData[0]->ledger;
            } else {
                $sickLeaveLedger = 0;
            }
            $sickObject->user_id = $user->id;
            $sickObject->leave_type = 'SICK LEAVE';
            $sickObject->value = 5;
            $sickObject->type = 'CREDIT';
            $sickObject->ledger = $sickObject->value + $sickLeaveLedger;

            $sickObject->save();

            /*
             * Casual Leave
             */
            $casualObject = new \App\LeaveTransaction();
            $casualLeaveData = \App\LeaveTransaction::where('user_id', '=', $user->id)
                ->where('leave_type', '=', 'CASUAL LEAVE')
                ->get();
            if ($casualLeaveData->toArray()) {
                $casualLeaveLedger = $casualLeaveData[0]->ledger;
            } else {
                $casualLeaveLedger = 0;
            }

            $casualObject->user_id = $user->id;
            $casualObject->leave_type = 'CASUAL LEAVE';
            $casualObject->value = 5;
            $casualObject->type = 'CREDIT';
            $casualObject->ledger = $casualObject->value + $casualLeaveLedger;

            $casualObject->save();

        }
    }

    /*
     * This function is for calculate the employee uninformed leave
     */
    public function calcUserUninformedAbsence()
    {
        /*
         * this is odd Saturday of current month
         */
        $OddSaturdayFirst = Carbon::parse('first saturday of this month');
        $OddSaturdayThird = Carbon::parse('third saturday of this month');
        $OddSaturdayFifth = Carbon::parse('fifth saturday of this month');

        $checkHoliday = Holiday::where('festivaldate', '=', Carbon::now()->toDateString())
            ->get();
        /*
         * if today is not oddSaturday or not sunday than execute below code
         */
        if ((Carbon::now() != $OddSaturdayFirst || Carbon::now() != $OddSaturdayThird || Carbon::now() != $OddSaturdayFifth) || (!Carbon::now()->isSunday()) || (!$checkHoliday)) {
            /*
             * Fetch all users
             */
            $users = User::all();
            foreach ($users as $user) {
                $userId = $user->id;
                /*
                 * check login.
                 */
                $checkLogin = Attendance::where('user_id', '=', $userId)
                    ->where('action_type', '=', 'LOGIN')
                    ->where('action_time', 'like', Carbon::now()->toDateString() . '%')
                    ->orderBy('id', 'DESC')
                    ->first();
                /*
                 * check logout.
                 */
                $checkLogout = Attendance::where('user_id', '=', $userId)
                    ->where('action_type', '=', 'LOGOUT')
                    ->where('action_time', 'like', Carbon::now()->toDateString() . '%')
                    ->orderBy('id', 'DESC')
                    ->first();
                /*
                 * if employee's login and logout time is not in database for particular day
                 * than execute below code.
                 */
                if (!$checkLogin && !$checkLogout) {
                    $checkLeave = Request::where('user_id', '=', $userId)
                        ->where('from_date', '<=', Carbon::now()->toDateString())
                        ->where('to_date', '>=', Carbon::now()->toDateString())
                        ->where('status', '=', 'APPROVED')
                        ->get();
                    if (!$checkLeave) {
                        /*
                     * Fetch latest privilege ledger of particular employee
                     */
                        $leaveData = \App\LeaveTransaction::where('user_id', '=', $userId)
                            ->where('leave_type', '=', 'PRIVILEGE LEAVE')
                            ->orderBy('id', 'DESC')
                            ->first();
                        if ($leaveData) {
                            $privilegeLedger = $leaveData->ledger;
                            if ($privilegeLedger >= 2) {

                                $unInformedLeave = new \App\LeaveTransaction();
                                $unInformedLeave->user_id = $userId;
                                $unInformedLeave->leave_type = 'PRIVILEGE LEAVE';
                                $unInformedLeave->value = 2;
                                $unInformedLeave->type = 'DEBIT';
                                $unInformedLeave->ledger = $privilegeLedger - 2;
                                $unInformedLeave->save();

                            } elseif ($privilegeLedger == 0) {

                                $unInformedLeave = new \App\LeaveTransaction();
                                $unInformedLeave->user_id = $userId;
                                $unInformedLeave->leave_type = 'LEAVE WITHOUT PAY';
                                $unInformedLeave->value = 2;
                                $unInformedLeave->type = 'DEBIT';
                                $unInformedLeave->ledger = 0;
                                $unInformedLeave->save();

                            } else {

                                $remainingPrivilegeLedger = 2 - $privilegeLedger;

                                $unInformedLeave = new \App\LeaveTransaction();
                                $unInformedLeave->user_id = $userId;
                                $unInformedLeave->leave_type = 'LEAVE WITHOUT PAY';
                                $unInformedLeave->value = $remainingPrivilegeLedger;
                                $unInformedLeave->type = 'DEBIT';
                                $unInformedLeave->ledger = 0;
                                $unInformedLeave->save();

                                $unInformedLeave = new \App\LeaveTransaction();
                                $unInformedLeave->user_id = $userId;
                                $unInformedLeave->leave_type = 'PRIVILEGE LEAVE';
                                $unInformedLeave->value = $remainingPrivilegeLedger;
                                $unInformedLeave->type = 'DEBIT';
                                $unInformedLeave->ledger = $remainingPrivilegeLedger - $unInformedLeave->value;
                                $unInformedLeave->save();

                            }
                        }
                    }
                }
            }
        } else {
            return true;
        }
    }
}
