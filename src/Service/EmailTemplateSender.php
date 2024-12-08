<?php declare(strict_types=1);

namespace App\Service;

use App\Enum\EmailTemplateUseInEnum;
use App\Repository\EmailTemplateRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final readonly class EmailTemplateSender
{
    public function __construct(
        private MailerInterface                     $mailer,
        private EmailTemplateRepository $emailTemplateRepository
    )
    {
    }

    public function sendEmail(string $toEmail, EmailTemplateUseInEnum $useIn, array $context): void
    {
        $template = $this->emailTemplateRepository->getByUsIn($useIn);

        $loader = new ArrayLoader([
            'subject' => $template->getSubject(),
            'message' => $template->getMessage()
        ]);

        $twig = new Environment($loader);

        $message = (new TemplatedEmail())
            ->to($toEmail)
            ->subject($twig->render('subject', $context))
            ->html($twig->render('message', $context));

        $this->mailer->send($message);
    }
}
