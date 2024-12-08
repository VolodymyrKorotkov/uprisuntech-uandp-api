<?php

namespace App\Entity\Sub;

use App\Entity\User;

interface EntityHasApplicationUserInterface
{
    public function hasUser(): bool;
    public function setUser(User $user): static;
}