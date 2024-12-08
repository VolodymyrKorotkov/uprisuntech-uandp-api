<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\FormProcessSubmissionVariable;

final class FormProcessSubmissionVariableCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FormProcessSubmissionVariable::class;
    }
}
