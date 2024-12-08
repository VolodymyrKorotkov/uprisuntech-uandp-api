<?php

namespace App\Enum;

use App\Entity\ApplicationType;
use App\Entity\FormIo;

enum ApplicationStrategyEnum: string
{
    case NATIVE = 'NATIVE';
    case CAMUNDA = 'CAMUNDA';

    public function getDefaultForm(ApplicationType $applicationType): FormIo
    {
        return match ($applicationType->getStrategyType()){
            self::NATIVE => $applicationType->getNativeStrategy()->getForm(),
            self::CAMUNDA => $applicationType->getCamundaStrategy()->getDefaultForm(),
        };
    }

    public function getTableForm(ApplicationType $applicationType): ?FormIo
    {
        return match ($applicationType->getStrategyType()){
            self::NATIVE => $applicationType->getNativeStrategy()->getTableForm(),
            self::CAMUNDA => $applicationType->getCamundaStrategy()->getTableForm(),
        };
    }
}
