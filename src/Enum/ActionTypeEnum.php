<?php
namespace App\Enum;
enum ActionTypeEnum: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}