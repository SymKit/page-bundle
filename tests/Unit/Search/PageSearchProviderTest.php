<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Search;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\PageBundle\Contract\PageRepositoryInterface;
use Symkit\PageBundle\Search\PageSearchProvider;

final class PageSearchProviderTest extends TestCase
{
    public function testGetCategoryReturnsTranslatedLabel(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->with('search.category.pages', [], 'SymkitPageBundle')->willReturn('Pages');

        $provider = new PageSearchProvider(
            $this->createMock(PageRepositoryInterface::class),
            $this->createMock(UrlGeneratorInterface::class),
            $translator,
        );
        self::assertSame('Pages', $provider->getCategory());
    }

    public function testGetPriorityReturnsTen(): void
    {
        $provider = new PageSearchProvider(
            $this->createMock(PageRepositoryInterface::class),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(TranslatorInterface::class),
        );
        self::assertSame(10, $provider->getPriority());
    }

    public function testSearchYieldsResultsFromRepository(): void
    {
        $page = new \Symkit\PageBundle\Entity\Page();
        $page->setTitle('About us');
        $page->setContent('Content');
        $page->setStatus(\Symkit\PageBundle\Entity\Page::STATUS_PUBLISHED);

        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->method('findForGlobalSearch')->with('about', 5)->willReturn([$page]);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->with('admin_page_edit', ['id' => null])->willReturn('https://example.com/admin/page/1');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('No route');

        $provider = new PageSearchProvider($repository, $urlGenerator, $translator);
        $results = iterator_to_array($provider->search('about'));
        self::assertCount(1, $results);
        self::assertSame('About us', $results[0]->title);
        self::assertSame('heroicons:document-text-20-solid', $results[0]->icon);
        self::assertNotEmpty($results[0]->url);
    }
}
