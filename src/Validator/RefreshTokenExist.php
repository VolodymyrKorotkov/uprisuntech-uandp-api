<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class RefreshTokenExist extends Constraint
{
	public string $message = 'The refresh token "{{ string }}" does not exist.';

	public function validatedBy()
	{
		return static::class . 'Validator';
	}
}
