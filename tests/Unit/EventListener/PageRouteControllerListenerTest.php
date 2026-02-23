<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symkit\PageBundle\Entity\Page;
use Symkit\PageBundle\EventListener\PageRouteControllerListener;

final class PageRouteControllerListenerTest extends TestCase
{
    private const ROUTE_CLASS = 'Symkit\RoutingBundle\Entity\Route';

    public function testPrePersistSetsControllerOnPageRouteWhenControllerIsNull(): void
    {
        if (!class_exists(self::ROUTE_CLASS)) {
            self::markTestSkipped('Symkit\RoutingBundle is not installed.');
        }

        $route = $this->createMock(self::ROUTE_CLASS);
        $route->method('getController')->willReturn(null);
        $route->expects(self::once())
            ->method('setController')
            ->with('App\Controller\PageController');

        $page = new Page();
        $page->setRoute($route);

        $args = new PrePersistEventArgs($page, $this->createMock(EntityManagerInterface::class));

        $listener = new PageRouteControllerListener(Page::class, 'App\Controller\PageController');
        $listener->prePersist($args);
    }

    public function testPrePersistDoesNotSetControllerWhenRouteAlreadyHasController(): void
    {
        if (!class_exists(self::ROUTE_CLASS)) {
            self::markTestSkipped('Symkit\RoutingBundle is not installed.');
        }

        $route = $this->createMock(self::ROUTE_CLASS);
        $route->method('getController')->willReturn('ExistingController');
        $route->expects(self::never())->method('setController');

        $page = new Page();
        $page->setRoute($route);

        $args = new PrePersistEventArgs($page, $this->createMock(EntityManagerInterface::class));

        $listener = new PageRouteControllerListener(Page::class, 'App\Controller\PageController');
        $listener->prePersist($args);
    }

    public function testPrePersistIgnoresNonPageEntity(): void
    {
        $args = new PrePersistEventArgs(
            new stdClass(),
            $this->createMock(EntityManagerInterface::class),
        );

        $listener = new PageRouteControllerListener(Page::class, 'App\Controller\PageController');
        $listener->prePersist($args);

        self::assertTrue(true, 'No exception when entity is not a Page');
    }
}
