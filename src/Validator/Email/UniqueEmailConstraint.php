<?php

namespace App\Validator\Email;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEmailConstraint extends Constraint
{
    public function validatedBy()
    {
        return static::class.'Validator';
    }
}