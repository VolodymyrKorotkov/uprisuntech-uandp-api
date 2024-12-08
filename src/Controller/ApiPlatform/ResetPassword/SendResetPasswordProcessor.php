<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\ResetPassword;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\ApiPlatform\ResetPassword\Dto\SendResetPasswordDto;
use App\Entity\User;
use App\Repository\SiteRedirectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

final readonly class SendResetPasswordProcessor implements ProcessorInterface
{
    private const RESET_PASSWORD_TEMPLATE = '@UserService/reset_password.html.twig';

    public function __construct(
        private UserRepository $userRepository,
        private SiteRedirectRepository $siteRedirectRepository,
        private MailerInterface $mailer
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws EntityNotFoundException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
       $this->doProcess($data);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws EntityNotFoundException
     */
    private function doProcess(SendResetPasswordDto $dto): void
    {
        $user = $this->userRepository->findByEmail($dto->email);
        if (!$user){
            throw new BadRequestHttpException('Email not found');
        }
        $user->setResetPasswordHash(uniqid());

        $message = (new TemplatedEmail())
            ->to($dto->email)
            ->subject('Reset password!')
            ->htmlTemplate(self::RESET_PASSWORD_TEMPLATE)
            ->context([
                'resetPasswordUrl' => $this->getResetPasswordUrl($user, $dto)
            ]);

        $this->mailer->send($message);
        $this->userRepository->save($user);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getResetPasswordUrl(User $user, SendResetPasswordDto $dto): string
    {
        $resetPasswordUrl = $this->siteRedirectRepository->getByAlias($dto->siteAlias)->getRedirectUrl();
        $queryString = http_build_query([
            'resetPasswordHash' => $user->getResetPasswordHash()
        ]);

        return $resetPasswordUrl . '?' . $queryString;
    }
}
