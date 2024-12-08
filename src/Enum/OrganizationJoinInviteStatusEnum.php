<?php

namespace App\Enum;

enum OrganizationJoinInviteStatusEnum: string
{
    case INVITED = 'INVITED';
    case CONFIRMED = 'CONFIRMED';
    case DRFO_CODE_NOT_MATCH = 'DRFO_CODE_NOT_MATCH';
    case ERROR_USER_HAS_ORGANIZATION = 'ERROR_USER_HAS_ORGANIZATION';
    case ERROR = 'ERROR';
}
