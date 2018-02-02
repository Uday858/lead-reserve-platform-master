<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user object.
     * @var $user
     */
    public $user;

    /**
     * Default user type for user creation.
     * @var $userType
     */
    public $userType;

    /**
     * Create a new event instance.
     */
    public function __construct($user,$userType)
    {
        // Assignment.
        $this->user = $user;
        $this->userType = $userType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.created');
    }
}
