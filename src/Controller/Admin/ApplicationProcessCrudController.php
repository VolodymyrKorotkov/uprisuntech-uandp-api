<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\ApplicationProcess;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

final class ApplicationProcessCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
       return ApplicationProcess::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from parent::configureFields($pageName);
        yield AssociationField::new('type')->autocomplete();
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Crud::PAGE_EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
         //   ->setDefaultSort(['completed' => 'ASC', 'updatedAt' => 'DESC'])
            ->overrideTemplate('crud/detail', 'admin_application_process_detail.html.twig')
             ->setPageTitle('edit', fn(ApplicationProcess $applicationProcess) => (string)$applicationProcess)
            ->setPageTitle('index', 'Processes');
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
}
