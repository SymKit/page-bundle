<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Sitemap;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symkit\MediaBundle\Service\MediaUrlGenerator;
use Symkit\PageBundle\Contract\PageRepositoryInterface;
use Symkit\PageBundle\Sitemap\PageSitemapLoader;

final class PageSitemapLoaderTest extends TestCase
{
    public function testCountDelegatesToRepository(): void
    {
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->expects(self::once())->method('countPublished')->willReturn(42);

        $loader = new PageSitemapLoader(
            $repository,
            $this->createUrlGenerator(),
            $this->createMock(MediaUrlGenerator::class),
        );
        self::assertSame(42, $loader->count());
    }

    public function testLoadYieldsSitemapUrlsFromPublishedPages(): void
    {
        $page = $this->createPageStub('page_1', '/about', '1.0', false);
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->method('findPublished')->with(10, 0)->willReturn([$page]);

        $urlGenerator = $this->createUrlGenerator();
        $urlGenerator->method('generate')->with('page_1', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/about');

        $loader = new PageSitemapLoader(
            $repository,
            $urlGenerator,
            $this->createMock(MediaUrlGenerator::class),
        );
        $items = iterator_to_array($loader->load(10, 0));
        self::assertCount(1, $items);
        self::assertSame('https://example.com/about', $items[0]->loc);
        self::assertSame('weekly', $items[0]->changefreq);
        self::assertSame('1.0', $items[0]->priority);
    }

    public function testLoadSkipsExcludedFromSitemap(): void
    {
        $page = $this->createPageStub('page_1', '/about', null, true);
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->method('findPublished')->willReturn([$page]);

        $loader = new PageSitemapLoader(
            $repository,
            $this->createUrlGenerator(),
            $this->createMock(MediaUrlGenerator::class),
        );
        $items = iterator_to_array($loader->load(10, 0));
        self::assertCount(0, $items);
    }

    private function createUrlGenerator(): UrlGeneratorInterface
    {
        $context = new RequestContext();
        $context->setScheme('https');
        $context->setHost('example.com');
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('getContext')->willReturn($context);

        return $urlGenerator;
    }

    private function createPageStub(string $routeName, string $path, ?string $sitemapPriority, bool $excludeFromSitemap): object
    {
        $route = new class($routeName, $path, $sitemapPriority, $excludeFromSitemap) {
            public function __construct(
                private readonly string $name,
                private readonly string $path,
                private readonly ?string $sitemapPriority,
                private readonly bool $excludeFromSitemap,
            ) {
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getPath(): ?string
            {
                return $this->path;
            }

            public function getSitemapPriority(): ?string
            {
                return $this->sitemapPriority;
            }

            public function isExcludeFromSitemap(): bool
            {
                return $this->excludeFromSitemap;
            }
        };

        $page = new class($route) {
            public function __construct(
                private readonly object $route,
            ) {
            }

            public function getRoute(): ?object
            {
                return $this->route;
            }

            public function getUpdatedAt(): ?DateTimeInterface
            {
                return new DateTimeImmutable();
            }

            public function getOgImage(): ?object
            {
                return null;
            }

            public function getTitle(): ?string
            {
                return 'Page';
            }
        };

        return $page;
    }
}
