<?php

namespace App\Service\GuideBook\Dto;

use App\ArgumentResolver\FromRequestDtoInterface;

final class GuideBookDelete implements FromRequestDtoInterface
{
    public int $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
