<?php declare(strict_types=1);

namespace App\Service\GovIdDecryptor\Dto;

final class GovUaDecryptorResultDto
{
    public function __construct(public string $decryptedUserData)
    {
    }

    public function getJsonDecodedData(): array
    {
        return json_decode($this->decryptedUserData, true);
    }
}