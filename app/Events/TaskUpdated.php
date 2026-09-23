<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $task
     */
    public function __construct(
        public int $projectId,
        public array $task,
        public int $progress,
        public bool $deleted = false,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('project.'.$this->projectId)];
    }

    public function broadcastAs(): string
    {
        return 'task.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'projectId' => $this->projectId,
            'task' => $this->task,
            'progress' => $this->progress,
            'deleted' => $this->deleted,
        ];
    }
}