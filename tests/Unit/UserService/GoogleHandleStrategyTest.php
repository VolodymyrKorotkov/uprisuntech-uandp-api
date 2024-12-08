<?php

namespace App\Tests\Unit\UserService;

use App\Entity\User;
use App\Enum\OauthTypeEnum;
use App\Repository\UserRepository;
use App\Service\OauthGoogleProvider\GoogleResourceOwnerProviderInterface;
use App\UserService\Service\OauthUserHandler\Dto\HandleOauthUserDto;
use App\UserService\Service\OauthUserHandler\Dto\HandleOauthUserResult;
use App\UserService\Service\OauthUserHandler\HandleStrategy\GoogleHandleStrategy;
use Google\Service\Oauth2\Userinfo;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class GoogleHandleStrategyTest extends TestCase
{
    private $googleResourceOwnerProvider;
    private $userRepository;
    private $googleHandleStrategy;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->googleResourceOwnerProvider = $this->createMock(GoogleResourceOwnerProviderInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->googleHandleStrategy = new GoogleHandleStrategy($this->googleResourceOwnerProvider, $this->userRepository);


    }

    public function testSupport()
    {
        // Arrange
        $dto = new HandleOauthUserDto(
            code: 'test_code',
            oauthType: OauthTypeEnum::GOOGLE
        );

        // Act
        $result = $this->googleHandleStrategy->support($dto);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @throws \Exception
     */
    public function testHandleOauthUser(): void
    {
        // Arrange
        $dto = new HandleOauthUserDto(
            code: 'test_code',
            oauthType: OauthTypeEnum::GOOGLE
        );
        $user = new User();
        $user->setEmail('test@gmail.com');

        $userinfoMock = $this->createMock(Userinfo::class);
        $userinfoMock->email = $user->getEmail();

        // Act
        $this->googleResourceOwnerProvider->expects($this->once())
            ->method('getResourceOwner')
            ->with($dto->code)
            ->willReturn($userinfoMock);


        // Assert
        $result = $this->googleHandleStrategy->handleOauthUser($dto);
        $this->assertInstanceOf(HandleOauthUserResult::class, $result);
        $this->assertTrue($result->isNewUser);
        $this->assertInstanceOf(UserInterface::class, $result->user);
        $this->assertEquals($user->getEmail(), $result->user->getEmail());
    }

}