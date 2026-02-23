<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Controller;

use Symkit\MenuBundle\Manager\MenuManager;
use Symkit\PageBundle\Metadata\PageBreadcrumbBuilder;
use Symkit\PageBundle\Repository\PageRepository;
use Symkit\PageBundle\Service\PageLayoutRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PageController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly MenuManager $menuManager,
        private readonly PageLayoutRegistry $layoutRegistry,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function __invoke(string $slug): Response
    {
        $page = $this->pageRepository->findBySlug($slug);

        if (!$page || !$page->isPublished()) {
            throw new NotFoundHttpException($this->translator->trans('error.page_not_found', [], 'SymkitPageBundle'));
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            $request->attributes->set(PageBreadcrumbBuilder::REQUEST_ATTRIBUTE_PAGE, $page);
            $request->attributes->set('_metadata_subject', $page);
        }

        $activeMenu = $page->getActiveMenu();
        $activeMenuItem = $page->getActiveMenuItem();
        if (null !== $activeMenu && null !== $activeMenuItem) {
            $menuCode = method_exists($activeMenu, 'getCode') ? $activeMenu->getCode() : (string) $activeMenu->getId();
            $itemId = method_exists($activeMenuItem, 'getIdentifier') ? $activeMenuItem->getIdentifier() : (string) $activeMenuItem->getId();
            $this->menuManager->setActiveId($menuCode, $itemId);
        }

        return $this->render('@SymkitPage/layout/bridge.html.twig', [
            'page' => $page,
            'layout' => $this->layoutRegistry->getLayoutPath($page->getTemplate()),
        ]);
    }
}
