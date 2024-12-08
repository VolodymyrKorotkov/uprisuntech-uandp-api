<?php

namespace App\Security;

interface EntityHasOwnerInterface
{
    public function getUserIdentifier(): string;
}
