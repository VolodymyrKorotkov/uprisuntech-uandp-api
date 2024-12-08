<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto;

final readonly class GetFormDto
{
    private const USER_FORM_ALIAS = 'user';

    public function __construct(
        public string $formKey
    )
    {
    }

    public static function newUserFormKey(): self
    {
        return new self(formKey: self::USER_FORM_ALIAS);
    }
}
