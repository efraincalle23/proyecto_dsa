<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;

class UserLogoutListener
{
    public function handle(Logout $event)
    {
        $user = $event->user;

        if ($user) {
            DB::table('user_sessions')->where('user_id', $user->id)->update([
                'is_active' => false
            ]);
        }
    }
}