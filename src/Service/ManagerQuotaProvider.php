<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\FormIoRepository;
use App\Service\FormIoClient\FormIoClient;
use Throwable;

final readonly class ManagerQuotaProvider
{
    public function __construct(
        private FormIoClient     $formIoClient,
        private FormIoRepository $formIoRepository
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function findConfirmedQuotaSubmissions(array $appNumbers, array $select = []): array
    {
        $form = $this->formIoRepository->getManagerProposalForm();

        return $this->formIoClient->getSubmissionList(
            formKey: $form->getFormKey(),
            filter: [
                $form->getFilterByApplicationNumberPath().'__in' => implode(',', $appNumbers),
                $form->getFilterByStatusPath() => $form->getConfirmedStatusValue(),
                'select' => $select
            ]
        );
    }
}
