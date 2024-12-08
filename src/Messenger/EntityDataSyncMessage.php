<?php

namespace App\Messenger;


class EntityDataSyncMessage
{
    public function __construct(
        public string $action,
        public array  $data
    )
    {
    }


    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'data' => $this->data
        ];
    }

}