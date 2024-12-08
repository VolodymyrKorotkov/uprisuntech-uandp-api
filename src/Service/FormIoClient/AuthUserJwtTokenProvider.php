<?php

namespace App\Service\FormIoClient;

use App\Service\AppUserProvider;
use App\Service\FormIoClient\Dto\GetFormDto;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Firebase\JWT\JWT;
use Psr\Cache\CacheItemInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Throwable;

final readonly class AuthUserJwtTokenProvider
{
    private const EXP_INTERVAL = '2 hours';

    public function __construct(
        #[Autowire(env: 'FORMIO_JWT_SECRET_KEY')] private string $jwtSecret,
        private FormIoSuperAdminClient                           $formIoSuperAdminClient,
        private Security                                         $security,
        private AppUserProvider                                  $userProvider,
        private CacheInterface                                   $cache
    )
    {
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function getJwtToken(): string
    {
        return $this->cache->get(
            $this->getCacheKey(),
            function(CacheItemInterface $item) {
                $item->expiresAt(
                    $this->getExpDateTime()->sub(\DateInterval::createFromDateString('5 sec'))
                );

                return $this->getJwtTokenByUserName();
            }
        );
    }

    /**
     * @throws Throwable
     */
    private function getRoleIds(): array
    {
        $userRoles = $this->security->getUser()->getRoles();
        $formioRoleIds = [];
        foreach ($this->formIoSuperAdminClient->getRoles() as $role) {
            if (in_array($role->title, $userRoles)) {
                $formioRoleIds[] = $role->id;
            }
        }

        return $formioRoleIds;
    }

    /**
     * @return \DateTime
     */
    private function getExpDateTime(): \DateTime
    {
        return (new \DateTime())
            ->add(
                \DateInterval::createFromDateString(self::EXP_INTERVAL)
            );
    }

    /**
     * @return string
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    private function getJwtTokenByUserName(): string
    {
        $form = $this->formIoSuperAdminClient->getForm(GetFormDto::newUserFormKey());

        return JWT::encode(
            payload: [
                "external" => true,
                "form" => [
                    "_id" => $form->id,
                    'project' => $form->project
                ],
                'project' => [
                    '_id' => $form->project
                ],
                "user" => [
                    "_id" => $this->security->getUser()->getUserIdentifier(),
                    "data" => [
                        "name" => (string)$this->userProvider->getUser(
                            $this->security->getUser()->getUserIdentifier()
                        )
                    ],
                    "roles" => $this->getRoleIds()
                ],
                'exp' => $this->getExpDateTime()->getTimestamp()
            ],
            key: $this->jwtSecret,
            alg: 'HS256'
        );
    }

    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        return 'formio_jwt_token_' . $this->security->getUser()->getUserIdentifier();
    }
}
