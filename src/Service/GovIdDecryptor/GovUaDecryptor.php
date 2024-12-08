<?php declare(strict_types=1);

namespace App\Service\GovIdDecryptor;

use App\Service\GovIdDecryptor\Dto\GetEncryptedUserDataDto;
use App\Service\GovIdDecryptor\Dto\GovUaDecryptorResultDto;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class GovUaDecryptor implements GovUaDecryptorInterface
{
    private string $privateKeyPath;
    private string $privateKeyPassword;
    private string $publicCertPath;
    private EUSignCP $euSign;

    private bool $initialized = false;

    /**
     * @throws Exception
     */
    public function __construct(
        #[Autowire('%kernel.project_dir%/%env(GOV_UA_PRIVATE_KEY_PATH)%')]
        string $privateKeyPath,
        #[Autowire(env: 'GOV_UA_PRIVATE_KEY_PASS')]
        string $privateKeyPassword,
        #[Autowire('%env(resolve:GOV_UA_CERTIFICATE)%')]
        string $publicCertPath
    )
    {
        $this->privateKeyPath = $privateKeyPath;
        $this->privateKeyPassword = $privateKeyPassword;
        $this->publicCertPath = $publicCertPath;
        $this->euSign = !empty($privateKeyPath) ? new EUSignCP() : null;
    }

    /**
     * @throws Exception
     */
    public function decryptUserData(GetEncryptedUserDataDto $dto): GovUaDecryptorResultDto
    {
        $this->initialize();

        $senderInfo = null;
        $errorCode = $this->euSign->develop(
            base64_decode($dto->encryptedUserData),
            $data,
            $senderInfo
        );

        $this->checkCryptoError($errorCode);

        return new GovUaDecryptorResultDto($data);
    }

    /**
     * @throws Exception
     */
    private function checkCryptoError(int $errorCode): void
    {
        if ($errorCode !== EUSignCP::EU_ERROR_NONE) {
            throw new Exception("Crypto error: " .
                $this->euSign->getErrorDescription($errorCode)
            );
        }
    }

    /**
     * @throws Exception
     */
    private function initialize(): void
    {
        if ($this->initialized){
            return;
        }

        $errorCode = $this->euSign->initialize(
            $this->privateKeyPath,
            $this->privateKeyPassword,
            $this->publicCertPath
        );

        $this->checkCryptoError($errorCode);

        $this->initialized = true;
    }
}
