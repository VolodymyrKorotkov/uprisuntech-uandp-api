<?php

namespace App\Security;

use Doctrine\ORM\QueryBuilder;

interface DataOnlyForOwnerRepositoryInterface
{
    public function handleQueryForOwner(QueryBuilder $qb, string $userIdentity, array $roles): void;
}
