<?php
namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;

class CodeNotFoundException extends \Exception
{
    protected $message = 'Code is invalid';
    protected $code = JsonResponse::HTTP_UNAUTHORIZED;
}
