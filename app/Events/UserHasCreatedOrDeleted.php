<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserHasCreatedOrDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $element_id;
    public $table;
    public $user_id;
    public $accion;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($element_id,$table,$user_id,$accion)
    {
        $this->element_id = $element_id;
        $this->table = $table;
        $this->user_id = $user_id;
        $this->accion = $accion;   
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
