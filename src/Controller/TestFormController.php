<?php declare(strict_types=1);

namespace App\Controller;

use App\Enum\AppRouteNameEnum;
use App\Security\ApplicationUserSecurity;
use App\Service\FormIoClient\FormIoClient;
use App\Service\FormioGetOrCreateProvider;
use App\Service\FormSubmissionEditLockerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

final class TestFormController extends AbstractController
{
    public function __construct(
        private readonly ApplicationUserSecurity $applicationUserSecurity,
        private readonly FormIoClient            $formIoClient
    )
    {
    }

    /**
     * @throws Throwable
     */
    #[Route('/test-formio/{formKey}/{submissionId}', name: AppRouteNameEnum::TEST_FORMIO->value)]
    public function testFormio(
        string $formKey,
        FormioGetOrCreateProvider $formioGetOrCreateProvider,
        FormSubmissionEditLockerService $editLockerService,
        string|null $submissionId = null
    ): Response
    {
        if ($this->applicationUserSecurity->isUserAuth()) {
            $submissions = $this->formIoClient->getSubmissionList($formKey);
        } else {
            $submissions = [];
        }

        return $this->render('test_formio.html.twig', [
            'submissions' => $submissions,
            'formKey' => $formKey,
            'submissionId' => $submissionId,
            'form' => $formioGetOrCreateProvider->getOrCreateByFormKey($formKey),
            'readOnly' => $submissionId && true === $editLockerService->isLocked($submissionId)
        ]);
    }
}
