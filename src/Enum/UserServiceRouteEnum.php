<?php declare(strict_types=1);

namespace App\Enum;

enum UserServiceRouteEnum: string
{
    case PREFIX = 'user-service';
    case ACCOUNT_PREFIX = 'account/user-service';
    case PUBLIC_PREFIX = 'public/user-service';
}
