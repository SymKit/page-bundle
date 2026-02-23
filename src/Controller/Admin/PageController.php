<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\CrudBundle\Controller\AbstractCrudController;
use Symkit\MenuBundle\Attribute\ActiveMenu;
use Symkit\MetadataBundle\Attribute\Breadcrumb;
use Symkit\MetadataBundle\Attribute\Seo;
use Symkit\PageBundle\Entity\Page;
use Symkit\PageBundle\Form\PageType;

final class PageController extends AbstractCrudController
{
    /**
     * @param class-string $pageClass
     */
    public function __construct(
        private readonly string $pageClass,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Seo(title: 'admin.page.list_title', description: 'admin.page.seo_list')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'admin.breadcrumb.pages', 'route' => 'admin_page_list']])]
    #[ActiveMenu('admin', 'pages')]
    public function list(Request $request): Response
    {
        return $this->renderIndex($request, [
            'page_title' => $this->translator->trans('admin.page.list_title', [], 'SymkitPageBundle'),
        ]);
    }

    #[Seo(title: 'admin.page.create_title')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'admin.breadcrumb.pages', 'route' => 'admin_page_list']])]
    #[ActiveMenu('admin', 'pages')]
    public function create(Request $request): Response
    {
        return $this->renderNew(new $this->pageClass(), $request, [
            'page_title' => $this->translator->trans('admin.page.create_title', [], 'SymkitPageBundle'),
        ]);
    }

    #[Seo(title: 'admin.page.edit_title')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'admin.breadcrumb.pages', 'route' => 'admin_page_list']])]
    #[ActiveMenu('admin', 'pages')]
    public function edit(Page $page, Request $request): Response
    {
        return $this->renderEdit($page, $request, [
            'page_title' => $this->translator->trans('admin.page.edit_title', [], 'SymkitPageBundle'),
            'template_vars' => [
                'page' => $page,
            ],
        ]);
    }

    public function delete(Page $page, Request $request): Response
    {
        return $this->performDelete($page, $request);
    }

    protected function getEntityClass(): string
    {
        return $this->pageClass;
    }

    protected function getFormClass(): string
    {
        return PageType::class;
    }

    protected function getRoutePrefix(): string
    {
        return 'admin_page';
    }

    protected function getNewTemplate(): string
    {
        return '@SymkitPage/admin/create.html.twig';
    }

    protected function getEditTemplate(): string
    {
        return '@SymkitPage/admin/edit.html.twig';
    }

    protected function configureListFields(): array
    {
        return [
            'id' => [
                'label' => 'admin.list.id',
                'sortable' => true,
            ],
            'title' => [
                'label' => 'admin.list.title',
                'sortable' => true,
            ],
            'status' => [
                'label' => 'admin.list.status',
                'template' => '@SymkitCrud/crud/field/status_badge.html.twig',
            ],
            'category' => [
                'label' => 'admin.list.category',
                'sortable' => true,
            ],
            'updatedAt' => [
                'label' => 'admin.list.updated',
                'template' => '@SymkitCrud/crud/field/date.html.twig',
            ],
            'actions' => [
                'label' => '',
                'template' => '@SymkitCrud/crud/field/actions.html.twig',
                'edit_route' => 'admin_page_edit',
                'header_class' => 'text-right',
                'cell_class' => 'text-right',
            ],
        ];
    }

    protected function configureSearchFields(): array
    {
        return ['title', 'content'];
    }
}
