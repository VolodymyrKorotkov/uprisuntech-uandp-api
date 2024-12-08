<?php

namespace App\Enum;

enum EmailTemplateUseInEnum: string
{
    case CREATE_USER = 'CREATE_USER';
    case INSTALLER_SEND_MESSAGE = 'INSTALLER_SEND_MESSAGE';
    case HAVE_NEW_QUOTA = 'HAVE_NEW_QUOTA';
}
