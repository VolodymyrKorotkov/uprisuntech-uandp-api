<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\EmailTemplate;
use App\Enum\EmailTemplateUseInEnum;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

final class EmailTemplateCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
       return EmailTemplate::class;
    }

    public function configureFields(string $pageName): iterable
    {
     //   yield from parent::configureFields($pageName);
        yield NumberField::new('id')->hideOnForm();
        yield TextareaField::new('subject');
        yield TextEditorField::new('message');
        yield ChoiceField::new('useIn')
            ->setChoices(EmailTemplateUseInEnum::cases());
    }
}
