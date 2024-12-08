<?php
namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;

class UserNotFoundException extends \Exception
{
    protected $message = 'User not found';
    protected $code = JsonResponse::HTTP_NOT_FOUND;
}
