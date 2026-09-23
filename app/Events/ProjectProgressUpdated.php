<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectProgressUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int $projectId,
        public int $progress,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('project.'.$this->projectId)];
    }

    public function broadcastAs(): string
    {
        return 'project.progress.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'projectId' => $this->projectId,
            'progress' => $this->progress,
        ];
    }
}