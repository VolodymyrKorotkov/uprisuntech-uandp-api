<?php declare(strict_types=1);

namespace App\Validator\EdrpouValidator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class Edrpou extends Constraint
{
    public string|null $message = null;

}