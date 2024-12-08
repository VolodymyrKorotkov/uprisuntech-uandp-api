<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\InstallerEmail;

final class InstallerEmailCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InstallerEmail::class;
    }
}
