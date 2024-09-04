<?php

namespace App\Events\DataCollectionRecord;

use App\Models\DataCollectionRecord;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataCollectionRecordUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DataCollectionRecord $dataCollectionRecord;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DataCollectionRecord $dataCollectionRecord)
    {
        $this->dataCollectionRecord = $dataCollectionRecord;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('channel-name');
    }
}
