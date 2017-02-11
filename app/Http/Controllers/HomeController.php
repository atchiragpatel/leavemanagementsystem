<?php

namespace App\Http\Controllers;

use App\LeaveTransaction;
use App\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\Exception;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd($this->calcAbsoluteLeaves('PRIVILEGE LEAVE', 1));
        //$days90 = Carbon::now()->addDays(90);
        //dd($this->calcAbsoluteLeaves('SICK', 1));
        //dd($this->calculatedPl(1));
        return view('home');
    }

    public function addEmployee()
    {
        return view('employee.addemployee');
    }

    public function employeeDetails(Request $request)
    {
        $users = new User;

        $users->fname = $request->fname;
        $users->mname = $request->mname;
        $users->lname = $request->lname;
        $users->salary = $request->salary;
        $users->doj = $request->doj;
        $users->email = $request->email;
        $users->password = bcrypt($request->password);
        $users->leave_taken_till_date = $request->leave_taken_till_date;

        $users->save();
        //If User Joining date is less then 3 months
        // Sick Leave & Casual Leave = 2
        // Privilege Leave = 0
        if ($users->doj > Carbon::now()->subMonth(+3)->toDateString()) {
            $leave = new LeaveTransaction();

            $leave->user_id = $users->id;
            $leave->leave_type = 'SICK LEAVE';
            $leave->value = 2;
            $leave->type = 'CREDIT';
            $leave->ledger = $leave->value;

            $leave->save();

            $leave = new LeaveTransaction();

            $leave->user_id = $users->id;
            $leave->leave_type = 'CASUAL LEAVE';
            $leave->value = 2;
            $leave->type = 'CREDIT';
            $leave->ledger = $leave->value;

            $leave->save();

            $leave = new LeaveTransaction();

            $leave->user_id = $users->id;
            $leave->leave_type = 'PRIVILEGE LEAVE';
            $leave->value = 0;
            $leave->type = 'CREDIT';
            $leave->ledger = $leave->value;

            $leave->save();
        }
        //If User Joining date is greater then 3 months
        // Sick Leave & Casual leave = 7
        // Privilege Leave  = Totaldays / 30 * 1.5.
        elseif ($users->doj < Carbon::now()->subMonth(+3)->toDateString()) {
            $leave = new LeaveTransaction();

            $leave->user_id = $users->id;
            $leave->leave_type = 'SICK LEAVE';
            $leave->value = 7;
            $leave->type = 'CREDIT';
            $leave->ledger = $leave->value;

            $leave->save();

            $leave = new LeaveTransaction();

            $leave->user_id = $users->id;
            $leave->leave_type = 'CASUAL LEAVE';
            $leave->value = 7;
            $leave->type = 'CREDIT';
            $leave->ledger = $leave->value;

            $leave->save();

            $endDate = Carbon::parse($users->doj);

            $currentDate = Carbon::now();
            $days = $endDate->diffInDays($currentDate);

            $calculateByDays = $days / 30;
            $absolutePrivilegeLeave = $calculateByDays * 1.5;

            $leave = new LeaveTransaction();

            $leave->user_id = $users->id;
            $leave->leave_type = 'PRIVILEGE LEAVE';
            $leave->value = $absolutePrivilegeLeave;
            $leave->type = 'CREDIT';
            $leave->ledger = $leave->value;

            $leave->save();
        }

        return redirect('listemployee');
    }

    public function listEmployee()
    {
        $users = User::all();
        return view('employee.listemployee')->with('users', $users);
    }

    public function editEmployee($id)
    {
        $user = User::find($id);
        return view('employee.editemployee')->with('user', $user);
    }

    public function storeEditEmployee($id, Request $request)
    {
        $userData = User::find($id);

        $userData->fname = $request->fname;
        $userData->mname = $request->mname;
        $userData->lname = $request->lname;
        $userData->salary = $request->salary;
        $userData->doj = $request->doj;
        $userData->email = $request->email;

        $userData->save();

        return redirect('listemployee');
    }

    public function deleteEmployee($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect('listemployee');
    }

    public function leavepolicy()
    {
        return view('leavepolicy');
    }

    /**
     * Calculate absolute leaves considering none were taken in the given period
     * @param string $type ["CASUAL", "SICK", "PRIVILEGE"]
     * @param int $userId Id of user that needs to calculate the leaves
     * @return float|int
     */
    public function calcAbsoluteLeaves($type, $userId)
    {

        // Get current date
        $currentDate = Carbon::now();

        // Try to parse given date with Carbon
        try {
            // Get joining date of user
            $dateString = \App\User::find($userId)->doj;
            if (!$dateString) {
                throw new \Exception("No joining date of user");
            }
            $date = new Carbon($dateString);
        } catch (\Exception $ex) {
            // on error or invalid date return 0
            return 0;
        }

        // Calculate the difference in days from current date
        // till provided date
        $diff = $date->diffInDays($currentDate);

        switch ($type) {
            // As casual and sick leave has same criteria we are
            // using same code for both
            case "SICK LEAVE":
            case "CASUAL LEAVE":
                // If provided date is less than three months than return 2
                // else on exact 90 days return 5 or else return 7
                return $diff < 90 ? 2 : ($diff == 90 ? 5 : 7);
                break;
            case "PRIVILEGE LEAVE":
                // No privilege leave should be granted within 3 months of tenure
                if ($diff < 90) {
                    return 0;
                }

                // Get first entry from database logs
                $firstLog = Attendance::where('user_id', '=', $userId)->first();

                // Get the difference of days from the non-logged days
                // i.e. JD - ( First Log date - 1) in days difference
                $diffInDays = $date->diffInDays(new Carbon($firstLog->created_at));

                // calculate it with 1.5 multiplier and 30 divider ( x * 1.5 / 30)
                $absoluteLeave = $diffInDays * 1.5 / 30;


                // Get the ledger balance (y) of requested leave and add it to the calculation
                // Thus total is (x *1.5/30) + y
                // Return this value
                $latestLedger = LeaveTransaction::where('user_id', '=', $userId)
                    ->where('leave_type', '=', $type)
                    ->orderBy('id', 'desc')
                    ->first()->ledger;
                return $absoluteLeave + $latestLedger;
                break;
            default:
                // On any other scenario other than PL, CL or SICK return 0;
                return 0;
        }
    }

    /**
     * @param $userId
     */
    public function calculatedPl($userId)
    {
        //Get user latest ledger
        $userLedger = LeaveTransaction::where('user_id', '=', $userId)
            ->where('leave_type', '=', 'PRIVILEGE LEAVE')
            ->orderBy('id', 'desc')
            ->first()->ledger;

        //Find Taken Leave from user table and subtract with user final privilege ledger
        $takenLeave = \App\User::find($userId)->leave_taken_till_date;

        return $userLedger - $takenLeave;

    }

    public function getLastLogoutDateOfUser()
    {

    }

    public function calcUserUninformedAbsense()
    {

    }
}
