<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\User;
use App\Enum\UserRoleEnum;
use App\Form\UserJwtTokenType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[IsGranted(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value)]
final class UserCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        foreach (parent::configureFields($pageName) as $field){
            yield $field;
        }
//        yield TextField::new('drfoCode');
//
        yield CollectionField::new('jwtTokens')
            ->setEntryType(UserJwtTokenType::class)
            ->setFormTypeOption('allow_delete', true)
            ->setFormTypeOption('allow_add', true)
            ->hideOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/edit', 'admin_user_edit.html.twig');
    }

    protected function getExcludeDefaultProperties(): array
    {
        return [
          //  'uuid',
            'passwordUpdatedDate',
            'resetPasswordHash',
            'token',
            'confirmEmailHash'
        ];
    }
}
