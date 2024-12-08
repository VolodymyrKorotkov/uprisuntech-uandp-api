<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Dto;

use App\Entity\FormIo;

final class HandleSubmissionSaveDto
{
    public function __construct(
        public HandleSubmissionSaveRequestDto $formioWebHookRequest,
        public FormIo                         $formIo
    )
    {
    }
}
