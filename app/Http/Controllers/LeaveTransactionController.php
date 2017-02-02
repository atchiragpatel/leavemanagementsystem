<?php

namespace App\Http\Controllers;

use App\LeaveTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveTransactionController extends Controller
{
    public function applyForLeave()
    {
        $user = Auth::user()->id;
        $leaves = LeaveTransaction::where('user_id','=',$user)
                                  ->where('created_at','like',Carbon::now()->subDay(+1)->toDateString().'%')->get();
        if ($leaves->toArray()) {
            foreach ($leaves as $leave) {
                switch ($leave['leave_type']) {
                    case "SICK LEAVE":
                        $sickLeaveLedger = $leave['ledger'];
                        break;
                    case "CASUAL LEAVE":
                        $casualLeaveLedger = $leave['ledger'];
                        break;
                    case "PRIVILEGE LEAVE":
                        $privilegeLeaveLedger = $leave['ledger'];
                        break;
                    default:
                        echo "No leaves remaining for you";
                }
            }
        }
        return view('leave.applyforleave')->with(['sickLeave' => $sickLeaveLedger,'casualleave' => $casualLeaveLedger,'privilegeleave' => $privilegeLeaveLedger]);
    }
}
