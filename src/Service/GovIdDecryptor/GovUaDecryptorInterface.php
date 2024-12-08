<?php

namespace App\Service\GovIdDecryptor;


use App\Service\GovIdDecryptor\Dto\GetEncryptedUserDataDto;
use App\Service\GovIdDecryptor\Dto\GovUaDecryptorResultDto;

interface GovUaDecryptorInterface
{
	public function decryptUserData(GetEncryptedUserDataDto $dto): GovUaDecryptorResultDto;
}