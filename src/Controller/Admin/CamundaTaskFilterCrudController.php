<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\CamundaTaskFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class CamundaTaskFilterCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CamundaTaskFilter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->hideOnForm();
        yield TextField::new('property')->setHelp('Example: "assigneeIn" or "assigneeLike"');
        yield TextField::new('value')->setHelp('Example: "{userIdentity},{userRoles}"');
        yield TextField::new('role');
    }
}
