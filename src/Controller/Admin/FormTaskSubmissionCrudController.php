<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\FormTaskSubmission;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

final class FormTaskSubmissionCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FormTaskSubmission::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from parent::configureFields($pageName);
        yield AssociationField::new('form')->autocomplete();
    }
}
