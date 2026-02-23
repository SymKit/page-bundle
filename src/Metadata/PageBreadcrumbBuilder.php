<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Metadata;

use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\MetadataBundle\Contract\BreadcrumbBuilderInterface;
use Symkit\MetadataBundle\Contract\BreadcrumbServiceInterface;

final readonly class PageBreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    public const REQUEST_ATTRIBUTE_PAGE = 'page';

    /**
     * @param class-string $pageClass
     */
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
        private string $pageClass,
        private string $pageRouteName,
    ) {
    }

    public function build(BreadcrumbServiceInterface $service): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $page = $request->attributes->get(self::REQUEST_ATTRIBUTE_PAGE);
        if (!$page instanceof $this->pageClass) {
            return;
        }

        \assert($page instanceof \Symkit\PageBundle\Entity\Page);
        $service->add($this->translator->trans('breadcrumb.home', [], 'SymkitPageBundle'), $this->urlGenerator->generate('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $activeMenuItem = $page->getActiveMenuItem();
        if (null !== $activeMenuItem) {
            $breadcrumbs = [];
            $item = $activeMenuItem;

            while (null !== $item) {
                $url = $item->getUrl();
                $route = $item->getRoute();
                if (null !== $route) {
                    try {
                        $url = $this->urlGenerator->generate($route->getName(), [], UrlGeneratorInterface::ABSOLUTE_URL);
                    } catch (Exception) {
                        $url = null;
                    }
                }

                array_unshift($breadcrumbs, [
                    'label' => $item->getLabel(),
                    'url' => $url,
                ]);

                $item = $item->getParent();
            }

            foreach ($breadcrumbs as $crumb) {
                if (null !== ($crumb['url'] ?? null)) {
                    $service->add($crumb['label'], $crumb['url']);
                }
            }
        }

        $currentUrl = $request->getUri();
        $service->add($page->getTitle() ?? $this->translator->trans('breadcrumb.page_default', [], 'SymkitPageBundle'), $currentUrl);
    }

    public function isRootRoute(string $route): bool
    {
        return 'app_home' === $route || $this->pageRouteName === $route;
    }
}
