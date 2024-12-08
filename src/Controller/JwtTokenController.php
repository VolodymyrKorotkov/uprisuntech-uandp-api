<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\CodeNotFoundException;
use App\Repository\UserRepository;
use App\Service\JwtTokenManager\AppJwtTokenManagerInterface;
use App\Service\JwtTokenManager\Dto\CreateJwtTokenDto;
use App\Service\JwtTokenManager\Dto\RefreshJwtTokenDto;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

final class JwtTokenController extends AbstractController
{
    public function __construct(
        private readonly AppJwtTokenManagerInterface $tokenManager,
        private readonly UserRepository              $userRepository,
    )
    {
    }

    /**
     * @throws CodeNotFoundException
     */
    #[Route('/api/v1/auth/token', methods: ['GET'])]
    public function createToken(#[MapQueryString] CreateJwtTokenDto $dto): JsonResponse
    {
        $user = $this->userRepository->findByCode($dto->code);
        if (!$user) {
            throw new CodeNotFoundException();
        }

        $user->removeCode();
        if (!$user->getToken()) {
            $user->setToken(uniqid());
        }

        $this->userRepository->save($user);

        return $this->json([
            'refresh_token' => null,
            'token' => $user->getToken()
        ]);
    }

    #[Route('/api/v1/auth/refresh-token', methods: ['GET'])]
    public function refreshToken(#[MapQueryString] RefreshJwtTokenDto $dto): JsonResponse
    {
        return $this->json($this->tokenManager->refreshJwtToken($dto));
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/v1/users/profile', methods: ['GET'])]
    public function profile(Request $request): JsonResponse
    {
        $bearerToken = $request->headers->get('Authorization');
        if (!$bearerToken){
            throw $this->createAccessDeniedException('Token is empty');
        }

        $token = str_replace('Bearer ', '', $bearerToken);

        return $this->json(
            data: $this->userRepository->getByToken($token),
            context: ['groups' => User::SAFE_GROUP]
        );
    }
}
