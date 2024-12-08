<?php

namespace App\Service\GuideBook\Dto;

use App\ArgumentResolver\FromRequestDtoInterface;
use App\Service\GuideBook\Enum\GuideBookEnableEnum;

final class GuideBookToggle implements FromRequestDtoInterface
{
    public int $id;
    public GuideBookEnableEnum $toggle;

    public function __construct(string $id, GuideBookEnableEnum $toggle)
    {
        $this->id = $id;
        $this->toggle = $toggle;
    }
}
