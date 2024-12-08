<?php

namespace App\Tests\Unit\UserService;

use App\Entity\User;
use App\Enum\OauthTypeEnum;
use App\Repository\GovUaDataRepository;
use App\Repository\UserRepository;
use App\Service\OauthGovIdProvider\Dto\GetGovIdResourceOwnerDto;
use App\Service\OauthGovIdProvider\Dto\GovIdResourceOwnerDto;
use App\Service\OauthGovIdProvider\GovIdResourceOwnerProviderInterface;
use App\UserService\Service\OauthUserHandler\Dto\HandleOauthUserDto;
use App\UserService\Service\OauthUserHandler\Dto\HandleOauthUserResult;
use App\UserService\Service\OauthUserHandler\HandleStrategy\GovIdUserHandleStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class GovIdUserHandleStrategyTest extends TestCase
{
    protected $govIdUserHandleStrategy;
    protected $govIdResourceOwnerProvider;
    protected $userRepository;
    protected $govUaDataRepository;

    public function setUp(): void
    {

        $this->govIdResourceOwnerProvider = $this->createMock(GovIdResourceOwnerProviderInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->govUaDataRepository = $this->createMock(GovUaDataRepository::class);
        $this->govIdUserHandleStrategy = new GovIdUserHandleStrategy($this->userRepository, $this->govUaDataRepository, $this->govIdResourceOwnerProvider);
    }

    public function testSupport()
    {
        // Arrange
        $dto = new HandleOauthUserDto(
            code: 'test_code',
            oauthType: OauthTypeEnum::GOV_ID
        );

        // Act
        $result = $this->govIdUserHandleStrategy->support($dto);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @throws \Exception
     */
    public function testHandleOauthUser()
    {
        // Arrange
        $dto = new HandleOauthUserDto(
            code: 'test_code',
            oauthType: OauthTypeEnum::GOV_ID
        );

        $user = new User();
        $user->setEmail('test@gmail.com');
        $user->setUuid('test');

        $resourceOwner = new GovIdResourceOwnerDto();
        $resourceOwner->email = $user->getEmail();
        $resourceOwner->drfocode = 'test_drfocode';



        $this->govIdResourceOwnerProvider->expects($this->once())
            ->method('getResourceOwner')
            ->with(new GetGovIdResourceOwnerDto(code: $dto->code))
            ->willReturn($resourceOwner);
        // Act
        $result = $this->govIdUserHandleStrategy->handleOauthUser($dto);

        // Assert
        $this->assertInstanceOf(HandleOauthUserResult::class, $result);
        $this->assertTrue($result->isNewUser);
        $this->assertInstanceOf(UserInterface::class, $result->user);
        $this->assertEquals($user->getEmail(), $result->user->getEmail());
    }


}