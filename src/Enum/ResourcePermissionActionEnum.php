<?php

namespace App\Enum;

enum ResourcePermissionActionEnum: string
{
    case EDIT = 'EDIT';
    case VIEW = 'VIEW';
    case DELETE = 'DELETE';
    case CREATE = 'CREATE';
}
