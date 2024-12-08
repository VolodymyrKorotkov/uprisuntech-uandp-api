<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\ConfirmEmail;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\ApiPlatform\ConfirmEmail\Dto\SendConfirmPasswordDto;
use App\Entity\User;
use App\Repository\SiteRedirectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class SendConfirmEmailProcessor implements ProcessorInterface
{
    private const CONFIRM_EMAIL_TEMPLATE = '@UserService/confirm_email.html.twig';

    public function __construct(
        private UserRepository $userRepository,
        private SiteRedirectRepository $siteRedirectRepository,
        private MailerInterface $mailer,
        private Security $security
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
    private function doProcess(SendConfirmPasswordDto $dto): void
    {
        $userInfo = $this->security->getUser()  ?? throw new AccessDeniedException();
        $user = $this->userRepository->findByEmail($userInfo->getUserIdentifier());
        if (!$user){
            throw new BadRequestHttpException('Email not found');
        }
        if($user->isIsVerifiedEmail()){
            throw new BadRequestHttpException('Email already confirmed');
        }

        $user->setConfirmEmailHash(uniqid());
        $message = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Confirm email password!')
            ->htmlTemplate(self::CONFIRM_EMAIL_TEMPLATE)
            ->context([
                'resetPasswordUrl' => $this->getConfirmEmailUrl($user, $dto)
            ]);

        $this->mailer->send($message);
        $this->userRepository->save($user);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getConfirmEmailUrl(User $user, SendConfirmPasswordDto $dto): string
    {
        $resetPasswordUrl = $this->siteRedirectRepository->getByAlias($dto->siteAlias)->getRedirectUrl();
        $queryString = http_build_query([
            'confirmEmailHash' => $user->getConfirmEmailHash()
        ]);
        return $resetPasswordUrl . '?' . $queryString;
    }
}
