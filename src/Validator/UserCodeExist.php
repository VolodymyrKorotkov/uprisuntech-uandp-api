<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UserCodeExist extends Constraint
{
	public string $message = 'The code "{{ string }}" does not exist.';

	public function validatedBy()
	{
		return static::class . 'Validator';
	}
}
