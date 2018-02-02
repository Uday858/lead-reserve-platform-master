<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\UserType;

class HandleNewUserCreation
{
    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        // Create a default user type for the user.
        UserType::create([
            'user_id' => $event->user->id,
            'type' => $event->userType
        ]);
    }
}