<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessageRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $owner;
    /**
     * Create a new event instance.
     */
    public function __construct($message, $owner)
    {
        $this->message = $message;
        $this->owner   = $owner;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting NewMessageRequest', ['message' => $this->message]);
        return [
            new PrivateChannel('user.' . $this->owner),
        ];
    }

    public function broadcastAs()
    {
        return "conversation.sender";
    }

    public function broadcastWith()
    {
        $owner         = User::find($this->owner);
        $conversations = $owner->conversations()
            ->orderBy("updated_at", 'desc')
            ->get();

        foreach ($conversations as $conversation) {
            $conversation['user']    = $conversation->users()->where("user_id", '!=', $owner->id)->first()->profile;
            $conversation['message'] = $conversation->messages()->orderBy("id", 'desc')->first();
        }

        return [
            'message'       => $this->message,
            'conversations' => $conversations,
        ];
    }
}
