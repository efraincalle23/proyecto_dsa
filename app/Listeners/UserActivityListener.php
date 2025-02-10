<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\DB;

class UserActivityListener
{
    public function handle(Authenticated $event)
    {
        $user = $event->user;

        DB::table('user_sessions')->updateOrInsert(
            ['user_id' => $user->id], // Buscar por user_id
            [
                'last_activity' => now(),
                'ip_address' => request()->ip(),
                'is_active' => true,
                'updated_at' => now()
            ]
        );
    }
}