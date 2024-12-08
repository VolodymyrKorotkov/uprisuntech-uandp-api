<?php

namespace App\Validator\KeycloakUserPassword;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class KeycloakUserPassword extends Constraint
{
    public function validatedBy()
    {
        return static::class.'Validator';
    }
}