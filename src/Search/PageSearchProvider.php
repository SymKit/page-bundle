<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Search;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\PageBundle\Repository\PageRepository;
use Symkit\SearchBundle\Contract\SearchProviderInterface;
use Symkit\SearchBundle\Model\SearchResult;

final readonly class PageSearchProvider implements SearchProviderInterface
{
    public function __construct(
        private PageRepository $pageRepository,
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
    ) {
    }

    public function search(string $query): iterable
    {
        $pages = $this->pageRepository->findForGlobalSearch($query);

        foreach ($pages as $page) {
            yield new SearchResult(
                title: $page->getTitle() ?? '',
                subtitle: $page->getRoute()?->getPath() ?? $this->translator->trans('search.no_route', [], 'SymkitPageBundle'),
                url: $this->urlGenerator->generate('admin_page_edit', ['id' => $page->getId()]),
                icon: 'heroicons:document-text-20-solid',
                badge: $page->getStatus(),
            );
        }
    }

    public function getCategory(): string
    {
        return $this->translator->trans('search.category.pages', [], 'SymkitPageBundle');
    }

    public function getPriority(): int
    {
        return 10;
    }
}
