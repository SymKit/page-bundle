<?php

declare(strict_types=1);

namespace Symkit\PageBundle\EventListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

final readonly class PageRouteControllerListener
{
    public function __construct(
        private string $pageClass,
        private string $frontController,
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->setControllerOnRoute($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->setControllerOnRoute($args->getObject());
    }

    private function setControllerOnRoute(object $entity): void
    {
        if (!$entity instanceof $this->pageClass) {
            return;
        }

        $route = $entity->getRoute();
        if (null === $route || null !== $route->getController()) {
            return;
        }

        $route->setController($this->frontController);
    }
}
