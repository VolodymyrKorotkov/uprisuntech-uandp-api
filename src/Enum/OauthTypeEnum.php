<?php

namespace App\Enum;

enum OauthTypeEnum: string
{
    case GOV_ID = 'id-gov-ua';
    case GOOGLE = 'google';
    case KEYCLOAK = 'keycloak';
}
