<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\Me;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Controller\ApiPlatform\AbstractObjectPopulateResolver;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.serializer.context_builder')]
final class CurrentUserObjectResolver extends AbstractObjectPopulateResolver
{
    public function __construct(
        SerializerContextBuilderInterface $decorated,
        private readonly Security $security,
        private readonly UserRepository $userRepository
    )
    {
        parent::__construct($decorated);
    }

    protected function getObject(): ?User
    {
        return $this->userRepository->getByUserIdentity(
            $this->security->getUser()->getUserIdentifier()
        );
    }
}
