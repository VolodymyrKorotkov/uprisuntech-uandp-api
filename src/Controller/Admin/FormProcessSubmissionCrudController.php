<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\FormProcessSubmission;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

final class FormProcessSubmissionCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FormProcessSubmission::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from parent::configureFields($pageName);
        yield AssociationField::new('form')->autocomplete();
    }
}
