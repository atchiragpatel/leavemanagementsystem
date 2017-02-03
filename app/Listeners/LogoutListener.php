<?php

namespace App\Listeners;

use App\Attendance;
use Carbon\Carbon;
use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Listener;

class LogoutListener extends Listener
{
    protected $user;
    protected $user_id;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Logout $event
     * @return void
     */
    public function handle(Logout $logoutUser)
    {
        $this->user_id = $logoutUser->user->id;

//        $logout = Attendance::where(['user_id' => $this->user_id, 'logout_time' => null])->first();
//        if ($logout) {
//            $logout->logout_time = Carbon::now();
//            $logout->save();
//        }
        $logout = new Attendance();
        $logout->user_id = $this->user_id;
        $logout->action_type = 'LOGOUT';
        $logout->action_time = Carbon::now();

        $logout->save();

    }
}
