<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserHasEdited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $element_org;
    public $element_final;
    public $table;
    public $user_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($element_org,$element_final,$table,$user_id)
    {
        $this->element_org = $element_org;
        $this->element_final = $element_final;
        $this->table = $table;
        $this->user_id = $user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
