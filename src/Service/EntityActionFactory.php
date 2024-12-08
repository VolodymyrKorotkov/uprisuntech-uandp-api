<?php declare(strict_types=1);

namespace App\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

final readonly class EntityActionFactory
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(
        AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator->unsetAll();
    }

    public function viewEntity(
        string $name,
        string $label,
        \Closure $entityIdGetter,
        string $controller
    ): Action
    {
        return Action::new($name, $label)
            ->displayAsLink()
            ->linkToUrl(
                fn(object $entity) => $this->buildViewEntityUrl($entityIdGetter($entity), $controller)
            );
    }

    public function editEntity(
        string $name,
        string $label,
        \Closure $entityIdGetter,
        string $controller
    ): Action
    {
        return Action::new($name, $label)
            ->displayAsLink()
            ->linkToUrl(
                fn(object $entity) => $this->buildEditEntityUrl($entityIdGetter($entity), $controller)
            );
    }

    public function associationEntitiesList(
        string $name,
        string $label,
        string $filterField,
        string $controller
    ): Action
    {
        return Action::new($name, $label)
            ->displayAsLink()
            ->linkToUrl(fn(object $entity) => $this->buildAssociationEntitiesListUrl($entity, $filterField, $controller));
    }

    private function buildAssociationEntitiesListUrl(object $entity, string $filterField, string $controller): string
    {
        return $this->adminUrlGenerator
            ->setController($controller)
            ->setAction(Action::INDEX)
            ->set('filters', [
                $filterField => [
                    "comparison" => "=",
                    "value" => $entity->getId()
                ]
            ])
            ->generateUrl();
    }

    private function buildViewEntityUrl(int $entityId, string $controller): string
    {
        return $this->adminUrlGenerator
            ->setController($controller)
            ->setEntityId($entityId)
            ->setAction(Action::DETAIL)
            ->generateUrl();
    }

    private function buildEditEntityUrl(int $entityId, string $controller): string
    {
        return $this->adminUrlGenerator
            ->setController($controller)
            ->setEntityId($entityId)
            ->setAction(Action::EDIT)
            ->generateUrl();
    }
}
