<?php

namespace App\Entity;

interface LockForUpdateEntityInterface
{
    public function isLockForUpdate(): bool;
}