<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Metadata;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\MetadataBundle\Contract\BreadcrumbServiceInterface;
use Symkit\PageBundle\Metadata\PageBreadcrumbBuilder;

final class PageBreadcrumbBuilderTest extends TestCase
{
    public function testIsRootRouteReturnsTrueForPageRouteName(): void
    {
        $builder = new PageBreadcrumbBuilder(
            new RequestStack(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
            \Symkit\PageBundle\Entity\Page::class,
            'page_show',
        );
        self::assertTrue($builder->isRootRoute('page_show'));
    }

    public function testIsRootRouteReturnsTrueForAppHome(): void
    {
        $builder = new PageBreadcrumbBuilder(
            new RequestStack(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
            \Symkit\PageBundle\Entity\Page::class,
            'page_show',
        );
        self::assertTrue($builder->isRootRoute('app_home'));
    }

    public function testIsRootRouteReturnsFalseForOtherRoute(): void
    {
        $builder = new PageBreadcrumbBuilder(
            new RequestStack(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
            \Symkit\PageBundle\Entity\Page::class,
            'page_show',
        );
        self::assertFalse($builder->isRootRoute('other_route'));
    }

    public function testBuildDoesNothingWhenRequestIsNull(): void
    {
        $service = $this->createMock(BreadcrumbServiceInterface::class);
        $service->expects(self::never())->method('add');

        $builder = new PageBreadcrumbBuilder(
            new RequestStack(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
        );
        $builder->build($service);
    }

    public function testBuildAddsHomeAndPageWhenPageInRequest(): void
    {
        $page = new \Symkit\PageBundle\Entity\Page();
        $page->setTitle('Test Page');

        $request = new Request();
        $request->attributes->set(PageBreadcrumbBuilder::REQUEST_ATTRIBUTE_PAGE, $page);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn('https://example.com/');
        $context = new RequestContext();
        $context->setScheme('https');
        $context->setHost('example.com');
        $urlGenerator->method('getContext')->willReturn($context);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id): string => match ($id) {
                'breadcrumb.home' => 'Home',
                'breadcrumb.page_default' => 'Page',
                default => $id,
            },
        );

        $service = $this->createMock(BreadcrumbServiceInterface::class);
        $service->expects(self::exactly(2))->method('add');
        $service->method('add')->willReturnCallback(function (string $label, string $url): void {
            static $call = 0;
            ++$call;
            if (1 === $call) {
                self::assertSame('Home', $label);
                self::assertSame('https://example.com/', $url);
            } else {
                self::assertSame('Test Page', $label);
                self::assertNotEmpty($url);
            }
        });

        $builder = new PageBreadcrumbBuilder($requestStack, $urlGenerator, $translator);
        $builder->build($service);
    }
}
