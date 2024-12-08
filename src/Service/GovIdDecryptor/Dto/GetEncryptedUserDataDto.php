<?php declare(strict_types=1);

namespace App\Service\GovIdDecryptor\Dto;

use App\ArgumentResolver\FromRequestDtoInterface;

final class GetEncryptedUserDataDto implements FromRequestDtoInterface
{
	public ?string $encryptedUserData = null;
}
