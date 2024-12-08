<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\OrganizationJoinFlowData;

final class OrganizationJoinFlowDataController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrganizationJoinFlowData::class;
    }
}
