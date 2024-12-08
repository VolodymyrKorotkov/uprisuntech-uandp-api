<?php

namespace App\Controller\ApiPlatform\UserByDrfo\Dto;

class DrfoCodeDto
{
    public function __construct(
        public string $drfoCode
    ){
    }
}