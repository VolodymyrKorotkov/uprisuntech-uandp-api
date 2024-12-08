<?php

namespace App\Enum;

enum OrganizationJoinStatusEnum: string
{
    case IN_PROGRESS = 'IN_PROGRESS';
    case CANCELED = 'CANCELED';
    case CONFIRMED = 'CONFIRMED';
}
