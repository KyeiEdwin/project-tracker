<?php

namespace App\Events;

use App\Models\TeamMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public TeamMessage $message)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('team.'.$this->message->team_id)];
    }

    public function broadcastAs(): string
    {
        return 'chat.message.created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return ['message' => $this->message->toInertia()];
    }
}
