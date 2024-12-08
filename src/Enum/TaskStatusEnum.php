<?php

namespace App\Enum;

enum TaskStatusEnum: string
{
    case IN_PROGRESS = 'IN_PROGRESS';
    case CANCEL = 'CANCEL';
    case RETURN_FOR_UPDATE = 'RETURN_FOR_UPDATE';
    case CONFIRMED = 'CONFIRMED';
}
