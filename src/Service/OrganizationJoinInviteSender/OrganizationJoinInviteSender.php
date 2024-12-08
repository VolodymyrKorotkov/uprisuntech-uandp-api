<?php declare(strict_types=1);

namespace App\Service\OrganizationJoinInviteSender;

use App\Entity\OrganizationJoinInvite;
use App\Enum\OauthParameterEnum;
use App\Enum\RedirectSiteAliasEnum;
use App\Repository\OrganizationJoinInviteRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

final readonly class OrganizationJoinInviteSender implements OrganizationJoinInviteSenderInterface
{
    private const EMAIL_TEMPLATE = 'email/organization_join_invite.html.twig';

    public function __construct(
        #[Autowire(env: 'APPLICATION_FLOW_INVITE_MANAGER_URL')] private string $joinUrl,
        private MailerInterface                                                $mailer,
        private LoggerInterface                                                $organizationInviteLogger,
        private OrganizationJoinInviteRepository                               $inviteRepository
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendOrganizationJoinInvite(OrganizationJoinInvite $invite): void
    {
        $invite->setInviteUrl(
            $this->getOrganizationJoinUrl($invite)
        );
        $this->inviteRepository->save($invite);

        $context = [
            'invite' => $invite,
            'organizationJoinUrl' => $invite->getInviteUrl()
        ];

        $subject = 'Join to organization - ' . $invite->getOrganization()->getTitle();

        $this->sendEmail($invite, $subject, $context);
        $this->organizationInviteLogger->debug($subject, $context);
    }

    private function getOrganizationJoinUrl(OrganizationJoinInvite $invite): string
    {
        return $this->joinUrl . '?' . urldecode(
                http_build_query([
                    OauthParameterEnum::SITE_ALIAS->value => RedirectSiteAliasEnum::ORGANIZATION_JOIN_INVITE->value,
                    OauthParameterEnum::STATE->value => $invite->getOauthUserState()
                ])
            );
    }

    /**
     * @param OrganizationJoinInvite $invite
     * @param string $subject
     * @param array $context
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendEmail(OrganizationJoinInvite $invite, string $subject, array $context): void
    {
        $message = (new TemplatedEmail())
            ->to($invite->getEmail())
            ->subject($subject)
            ->htmlTemplate(self::EMAIL_TEMPLATE)
            ->context($context);
        $this->mailer->send($message);
    }
}
