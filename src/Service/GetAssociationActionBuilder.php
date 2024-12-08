<?php declare(strict_types=1);

namespace App\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

final class GetAssociationActionBuilder
{
    private string $controller;
    private string $filterField;
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator

    )
    {
    }

    public function setFilterField(string $filterField): self
    {
        $this->filterField = $filterField;
        return $this;
    }

    public function setController(string $controller): self
    {
        $this->controller = $controller;
        return $this;
    }

    public function createAction(string $name, string $label): Action
    {
        return Action::new($name, $label)
            ->displayAsLink()
            ->linkToUrl(fn(object $entity) => $this->buildProcessInstancesUrl($entity));
    }

    private function buildProcessInstancesUrl(object $entity): string
    {
        return $this->adminUrlGenerator
            ->setController($this->controller)
            ->setAction(Action::INDEX)
            ->set('filters', [
                $this->filterField => [
                    "comparison" => "=",
                    "value" => $entity->getId()
                ]
            ])
            ->generateUrl();
    }
}
