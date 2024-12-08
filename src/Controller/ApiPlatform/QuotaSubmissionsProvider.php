<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\FormIoRepository;
use App\Service\FormIoClient\FormIoClient;
use App\Service\ManagerQuotaProvider;
use App\Service\ProcessSubmissionVariable\SubmissionPropertyAccessor;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Throwable;

final readonly class QuotaSubmissionsProvider implements ProviderInterface
{
    public function __construct(
        private FormIoClient $formIoClient,
        private Security $security,
        private FormIoRepository $formIoRepository,
        private SubmissionPropertyAccessor $submissionPropertyAccessor,
        private ManagerQuotaProvider $managerQuotaProvider
    )
    {
    }

    /**
     * @throws Throwable
     * @throws EntityNotFoundException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $appForm = $this->formIoRepository->getApplicationPublicForm();

        $applications = $this->formIoClient->getSubmissionList(
            formKey: $appForm->getFormKey(),
            filter: [
                'owner' => $this->security->getUser()->getUserIdentifier()
            ]
        );

        $applicationNumbers = [];
        foreach ($applications as $applicationSubmission){
            $applicationNumbers[] = $this->submissionPropertyAccessor->getApplicationNumber($applicationSubmission, $appForm);
        }

        $managerForm = $this->formIoRepository->getManagerProposalForm();
        $quotas = $this->managerQuotaProvider->findConfirmedQuotaSubmissions($applicationNumbers, [
            '_id',
            $managerForm->getFilterByApplicationNumberPath()]
        );

        $quotasCount = [];
        foreach ($quotas as $quota){
            $number = $this->submissionPropertyAccessor->getApplicationNumber($quota, $managerForm);
            if (isset($quotasCount[$number])){
                $quotasCount[$number]++;
            } else {
                $quotasCount[$number] = 1;
            }
        }

        foreach ($applications as $applicationSubmission){
            $number = $this->submissionPropertyAccessor->getApplicationNumber($applicationSubmission, $appForm);
            $applicationSubmission->extraData['quotasCount'] = $quotasCount[$number] ?? 0;
        }

        return $applications;
    }
}
