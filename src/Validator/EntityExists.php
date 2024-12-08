<?php declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class EntityExists extends Constraint
{
    public string|null $message = null;
}