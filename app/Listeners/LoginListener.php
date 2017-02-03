<?php

namespace App\Listeners;

use App\Attendance;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Listener;
use Illuminate\Support\Facades\Auth;

class LoginListener extends Listener
{
    protected $user_id;
    protected $user;
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
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $loginUser)
    {
        $this->user_id = $loginUser->user->id;

        $login = new Attendance();
        $login->user_id = $this->user_id;
        $login->action_type = 'LOGIN';
        $login->action_time = Carbon::now();

        $login->save();
    }
}
