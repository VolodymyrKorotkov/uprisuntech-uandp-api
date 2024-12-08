<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\NativeStrategy;
use App\Enum\ApplicationStrategyEnum;
use App\Enum\EntityCrudRoleEnum;
use App\Enum\UserRoleEnum;
use Closure;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class NativeStrategyCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NativeStrategy::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->hideOnForm();
        yield TextField::new('title');
        yield TextField::new('alias');
        yield TextField::new('role');
        yield BooleanField::new('enabled');
        yield BooleanField::new('neverComplete');
        yield BooleanField::new('assignForProcessStarter');
        yield AssociationField::new('form')->autocomplete();
        yield AssociationField::new('tableForm')->autocomplete();
        yield AssociationField::new('nextType')
            ->autocomplete()
            ->setQueryBuilder($this->getQueryBuilderCallableForNativeType());
    }

    /**
     * @return Closure
     */
    private function getQueryBuilderCallableForNativeType(): Closure
    {
        return fn(QueryBuilder $queryBuilder) => $queryBuilder
            ->andWhere('entity.strategyType = :nativeStrategy')
            ->setParameter('nativeStrategy', ApplicationStrategyEnum::NATIVE);
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
}
