<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\ApplicationType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class ApplicationTypeCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ApplicationType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->hideWhenCreating()->hideWhenUpdating();
        yield TextField::new('title');
        yield TextField::new('alias');
        yield TextField::new('role');
        yield BooleanField::new('allowStartProcess');
        yield BooleanField::new('enabled');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)->disable(Action::NEW);
    }
}
