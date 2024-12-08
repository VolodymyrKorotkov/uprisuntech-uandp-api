<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\FormSubmissionEditLocker;

final class FormSubmissionEditLockerCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FormSubmissionEditLocker::class;
    }
}
