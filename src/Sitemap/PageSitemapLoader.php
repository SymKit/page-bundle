<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Sitemap;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symkit\MediaBundle\Service\MediaUrlGenerator;
use Symkit\PageBundle\Contract\PageRepositoryInterface;
use Symkit\SitemapBundle\Contract\SitemapLoaderInterface;
use Symkit\SitemapBundle\Model\SitemapUrl;

final readonly class PageSitemapLoader implements SitemapLoaderInterface
{
    public function __construct(
        private PageRepositoryInterface $repository,
        private UrlGeneratorInterface $urlGenerator,
        private MediaUrlGenerator $mediaUrlGenerator,
    ) {
    }

    public function count(): int
    {
        return $this->repository->countPublished();
    }

    /**
     * @return iterable<SitemapUrl>
     */
    public function load(int $limit, int $offset): iterable
    {
        $pages = $this->repository->findPublished($limit, $offset);

        foreach ($pages as $page) {
            $route = $page->getRoute();
            if (!$route) {
                continue;
            }

            if ($route->isExcludeFromSitemap()) {
                continue;
            }

            $priority = null !== $route->getSitemapPriority()
                ? (string) $route->getSitemapPriority()
                : $this->defaultPriorityForPath($route->getPath());

            $images = [];
            if ($ogImage = $page->getOgImage()) {
                if ($url = $this->mediaUrlGenerator->generateUrl($ogImage)) {
                    $baseUrl = $this->urlGenerator->getContext()->getScheme().'://'.$this->urlGenerator->getContext()->getHost();
                    $images[] = [
                        'loc' => $baseUrl.$url,
                        'title' => $ogImage->getAltText() ?? $page->getTitle(),
                    ];
                }
            }

            yield new SitemapUrl(
                loc: $this->urlGenerator->generate($route->getName(), [], UrlGeneratorInterface::ABSOLUTE_URL),
                lastmod: $page->getUpdatedAt(),
                changefreq: 'weekly',
                priority: $priority,
                images: $images,
            );
        }
    }

    private function defaultPriorityForPath(string $path): string
    {
        if ('/' === $path || '' === $path) {
            return '1.0';
        }

        return '0.8';
    }
}
