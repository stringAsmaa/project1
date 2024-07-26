<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class chat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $to_id;

    /**
     * Create a new event instance.
     */
    public function __construct($message,$to_id)
    {
        $this->message=$message;
        $this->to_id=$to_id;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    { //اسم القناة 
        return [
            new PrivateChannel('chatLaravel'),
        ];
    }

    
    public function broadcastAs(){

//اسم ال event
        return 'chat';
        }
}
