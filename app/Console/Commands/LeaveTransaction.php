<?php

namespace App\Console\Commands;

use App\User;
use App\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        //$lastProcessedDate = new Carbon($lastProcessedDate->action_time);
        $lastProcessedDate = new Carbon('2 feb 2017');
        $currentDate = Carbon::now();

        // Process all the entries from that date on to current day
        while ($lastProcessedDate < $currentDate) {

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

            foreach ($arr as $userId => $userAttendance) {

                $isFullDay = false;
                $hasLoginBeforeOfficialStart = !!count(array_filter($userAttendance, function ($attendance) use ($officialDayStartWithBuffer) {
                    return $attendance["action_type"] == "LOGIN" && new Carbon($attendance["action_time"]) <= $officialDayStartWithBuffer;
                }));

                $hasLogoutAfterOfficialEnd = !!count(array_filter($userAttendance, function ($attendance) use ($officialDayEndWithBuffer) {
                    return $attendance["action_type"] == "LOGOUT" && new Carbon($attendance["action_time"]) >= $officialDayEndWithBuffer;
                }));

                if ($hasLoginBeforeOfficialStart && $hasLogoutAfterOfficialEnd) {
                    $isFullDay = true;

                    $temp1 = Carbon::parse($userAttendance[1]['action_time']);
                    $temp2 = Carbon::parse($userAttendance[0]['action_time']);
                    $temp = $temp1->diffInHours($temp2);
                    
                    dd($temp);
                }
            }

        }
    }

    public function sickCasualLeaves()
    {
        $users = User::where('doj', '=', Carbon::now()->subMonth(+3)->toDateString())->get();

        foreach ($users as $user) {
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
        }

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
