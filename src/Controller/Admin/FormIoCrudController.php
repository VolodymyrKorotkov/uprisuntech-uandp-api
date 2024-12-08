<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\FormIo;
use App\Enum\AppRouteNameEnum;
use App\Enum\EntityCrudRoleEnum;
use App\Enum\UserRoleEnum;
use App\Form\FormIoProcessResourceType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class FormIoCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FormIo::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->hideOnForm();
        yield TextField::new('title')->hideOnForm();
        yield TextField::new('formKey');
        yield TextField::new('formId')->hideOnForm();

        yield CollectionField::new('processResources')
            ->setEntryIsComplex(true)
            ->setEntryType(FormIoProcessResourceType::class)
            ->setFormTypeOption('allow_delete', true)
            ->setFormTypeOption('allow_add', true)
            ->hideOnIndex();

        yield BooleanField::new('applicationPublicForm');
        yield BooleanField::new('installerProposalForm');
        yield BooleanField::new('managerProposalForm');

        yield AssociationField::new('startedProcessType')->autocomplete();

        yield TextField::new('filterByApplicationNumberPath')->hideOnIndex();
        yield TextField::new('filterByStatusPath')->hideOnIndex();
        yield TextField::new('confirmedStatusValue')->hideOnIndex();
        yield TextField::new('draftStatusValue')->hideOnIndex();
        yield TextField::new('statusPath')->hideOnIndex();

        yield TextField::new('emailPropertyPath')->hideOnIndex();
        yield TextField::new('applicationNumberPropertyPath')->hideOnIndex();
        yield TextField::new('firstNamePropertyPath')->hideOnIndex();
        yield TextField::new('lastNamePropertyPath')->hideOnIndex();

        yield TextField::new('addressPropertyPath')->hideOnIndex();
        yield TextField::new('zipcodePropertyPath')->hideOnIndex();

        yield TextField::new('applicationResourcePath')->hideOnIndex();

    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->overrideTemplate('crud/edit', 'admin_form_edit.html.twig')
            ->setPageTitle('edit', fn(FormIo $formIo) => 'Edit ' . $formIo)
            ->setPageTitle('new', 'Create new Form')
            ->setPageTitle('index', 'Forms');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn(Action $act) => $act->setLabel('Create new form')
            )
            ->add(Crud::PAGE_INDEX, $this->createTestFormioAction()->displayAsLink())
            ->add(Crud::PAGE_EDIT, $this->createTestFormioAction()->displayAsLink())
            ->add(Crud::PAGE_DETAIL, $this->createTestFormioAction()->displayAsLink());
    }

    public static function getViewRole(): UserRoleEnum
    {
        return EntityCrudRoleEnum::newFromEntityClass(self::getEntityFqcn())->getRoleView();
    }

    protected static function getNewRole(): UserRoleEnum
    {
        return EntityCrudRoleEnum::newFromEntityClass(self::getEntityFqcn())->getRoleCreate();
    }

    protected static function getEditRole(): UserRoleEnum
    {
        return EntityCrudRoleEnum::newFromEntityClass(self::getEntityFqcn())->getRoleEdit();
    }

    protected static function getDeleteRole(): UserRoleEnum
    {
        return EntityCrudRoleEnum::newFromEntityClass(self::getEntityFqcn())->getRoleDelete();
    }

    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
//            ->addCssFile(
//                Asset::new('https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css')->ignoreOnIndex()
//            )
            ->addCssFile(
                Asset::new('https://cdn.form.io/formiojs/formio.full.min.css')->ignoreOnIndex()
            )
            ->addJsFile(
                Asset::new('https://cdn.form.io/formiojs/formio.full.min.js')->ignoreOnIndex()
            );
    }

    private function createTestFormioAction(): Action
    {
        return Action::new('testFormio', 'Test Form')
            ->linkToUrl(
                fn(FormIo $formIo) => $this->generateUrl(
                    AppRouteNameEnum::TEST_FORMIO->value,
                    ['formKey' => $formIo->getFormKey()]
                )
            );
    }
}
