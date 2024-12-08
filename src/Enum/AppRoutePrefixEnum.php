<?php declare(strict_types=1);

namespace App\Enum;

enum AppRoutePrefixEnum: string
{
    case API_ACCOUNT = 'account';
    case API_PUBLIC = 'public';
    case ADMIN = 'admin';
    case API_ADMIN = 'api/admin';
}
