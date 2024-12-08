<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\FormProcessSubmissionMultiple;
use App\Form\FormProcessSubmissionMultipleItemType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

final class FormProcessSubmissionMultipleCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FormProcessSubmissionMultiple::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from parent::configureFields($pageName);
        yield AssociationField::new('form')->autocomplete();
        yield CollectionField::new('submissionIds', 'Submission Ids')
            ->setEntryIsComplex(true)
            ->setEntryType(FormProcessSubmissionMultipleItemType::class)
            ->setFormTypeOption('allow_delete', true)
            ->setFormTypeOption('allow_add', true)
            ->hideOnIndex();
    }
}
