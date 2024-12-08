<?php

namespace App\Validator\Password;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PasswordConstraint extends Constraint
{
    public function validatedBy()
    {
        return static::class.'Validator';
    }
}