<?php declare(strict_types=1);

namespace App\Controller;

use App\Enum\UserRoleEnum;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AppAbstractCrudController extends AbstractCrudController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value)) {
            return $queryBuilder;
        }

        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository($entityDto->getFqcn());
        if (!$repository instanceof DataOnlyForOwnerRepositoryInterface) {
            return $queryBuilder;
        }

        $repository->handleQueryForOwner($queryBuilder, $this->getUser()->getUserIdentifier(), $this->getUser()->getRoles());
        return $queryBuilder;
    }

    protected function getExcludeDefaultProperties(): array
    {
        return [];
    }

    public function configureFields(string $pageName): iterable
    {
        /**
         * @var FieldInterface[] $fields
         */
        $fields = parent::configureFields($pageName);
        foreach ($fields as $field) {
            if (in_array($field->getAsDto()->getProperty(), $this->getExcludeDefaultProperties())) {
                continue;
            }

            yield $field;
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDoctrine(): ManagerRegistry
    {
        return $this->container->get('doctrine');
    }

    public static function getViewRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_SUPER_ADMIN_CASE;
    }

    protected static function getEditRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_SUPER_ADMIN_CASE;
    }

    protected static function getNewRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_SUPER_ADMIN_CASE;
    }

    protected static function getDeleteRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_SUPER_ADMIN_CASE;
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->setPermission(Action::DETAIL, $this->getViewRole()->value)
            ->setPermission(Action::INDEX, $this->getViewRole()->value)
            ->setPermission(Action::EDIT, $this->getEditRole()->value)
            ->setPermission(Action::NEW, $this->getNewRole()->value)
            ->setPermission(Action::DELETE, $this->getDeleteRole()->value);
    }

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        $responseParameters = parent::configureResponseParameters($responseParameters);
        $responseParameters->set('dataController', $this->getStimulusDataController($responseParameters));

        return $responseParameters;
    }

    private function getStimulusDataController(KeyValueStore $responseParameters): string
    {
        return $this->getStimulusEntityShortName() . '-' . $responseParameters->get('pageName');
    }

    private function getStimulusEntityShortName(): string
    {
        return str_replace(
            search: ['\\', '-controller'],
            replace: '',
            subject: strtolower(
                preg_replace(
                    pattern: '/(?<!^)[A-Z]/',
                    replacement: '-$0',
                    subject: (new \ReflectionClass(static::class))->getShortName()
                )
            )
        );
    }
}
