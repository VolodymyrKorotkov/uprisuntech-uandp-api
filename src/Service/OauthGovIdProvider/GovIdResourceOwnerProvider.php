<?php declare(strict_types=1);

namespace App\Service\OauthGovIdProvider;

use App\Serializer\AppJsonNormalizerInterface;
use App\Service\GovIdDecryptor\Dto\GetEncryptedUserDataDto;
use App\Service\GovIdDecryptor\Dto\GovUaDecryptorResultDto;
use App\Service\GovIdDecryptor\GovUaDecryptorInterface;
use App\Service\OauthGovIdProvider\Dto\GetGovIdResourceOwnerDto;
use App\Service\OauthGovIdProvider\Dto\GovIdResourceOwnerDto;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias]
final readonly class GovIdResourceOwnerProvider implements GovIdResourceOwnerProviderInterface
{
    public function __construct(
        private GovIdProvider              $govIdUaProvider,
        private GovUaDecryptorInterface    $govUaDecryptor,
        private AppJsonNormalizerInterface $appJsonNormalizer
    )
    {
    }

    /**
     * @throws IdentityProviderException
     */
    public function getResourceOwner(GetGovIdResourceOwnerDto $dto): GovIdResourceOwnerDto
    {
        try {
            $encryptedData = $this->getEncryptedData($dto);
        } catch (\Throwable){
            $encryptedData = $this->getEncryptedData($dto);
        }

        $result = $this->getResourceOwnerResult($encryptedData);
        $result->jsonData = $encryptedData;

        return $result;
    }

    /**
     * @throws IdentityProviderException
     */
    private function getAccessToken(GetGovIdResourceOwnerDto $dto): AccessToken
    {
        return $this->govIdUaProvider->getAccessToken('authorization_code', [
            'code' => $dto->code
        ]);
    }

    private function decode(array $encryptedData): GovUaDecryptorResultDto
    {
        $dto = new GetEncryptedUserDataDto;
        $dto->encryptedUserData = $encryptedData['encryptedUserInfo'] ?? '';

        return $this->govUaDecryptor->decryptUserData($dto);
    }

    private function getResourceOwnerResult(array $encryptedData): GovIdResourceOwnerDto
    {
        $decodedData = $this->decode($encryptedData)->getJsonDecodedData();

        $result = $this->appJsonNormalizer->denormalize(
            data: $decodedData,
            type: GovIdResourceOwnerDto::class
        );
        $result->jsonData = $decodedData;

        return $result;
    }

    /**
     * @param GetGovIdResourceOwnerDto $dto
     * @return array
     * @throws IdentityProviderException
     */
    public function getEncryptedData(GetGovIdResourceOwnerDto $dto): array
    {
        $token = $this->getAccessToken($dto);

        return $this->govIdUaProvider->getResourceOwner($token)->toArray();
    }
}
