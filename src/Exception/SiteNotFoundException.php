<?php
namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;

class SiteNotFoundException extends \Exception
{
    protected $message = 'Redirect url not valid';
    protected $code = JsonResponse::HTTP_NOT_FOUND;
}
