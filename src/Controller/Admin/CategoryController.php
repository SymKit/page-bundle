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
use Symkit\PageBundle\Entity\Category;
use Symkit\PageBundle\Form\CategoryType;

final class CategoryController extends AbstractCrudController
{
    /**
     * @param class-string $categoryClass
     */
    public function __construct(
        private readonly string $categoryClass,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Seo(title: 'admin.category.list_title', description: 'admin.category.seo_list')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'admin.breadcrumb.categories', 'route' => 'admin_category_list']])]
    #[ActiveMenu('admin', 'categories')]
    public function list(Request $request): Response
    {
        return $this->renderIndex($request, [
            'page_title' => $this->translator->trans('admin.category.list_title', [], 'SymkitPageBundle'),
        ]);
    }

    #[Seo(title: 'admin.category.create_title')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'admin.breadcrumb.categories', 'route' => 'admin_category_list']])]
    #[ActiveMenu('admin', 'categories')]
    public function create(Request $request): Response
    {
        return $this->renderNew(new $this->categoryClass(), $request, [
            'page_title' => $this->translator->trans('admin.category.create_title', [], 'SymkitPageBundle'),
        ]);
    }

    #[Seo(title: 'admin.category.edit_title')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'admin.breadcrumb.categories', 'route' => 'admin_category_list']])]
    #[ActiveMenu('admin', 'categories')]
    public function edit(Category $category, Request $request): Response
    {
        return $this->renderEdit($category, $request, [
            'page_title' => $this->translator->trans('admin.category.edit_title', [], 'SymkitPageBundle'),
        ]);
    }

    public function delete(Category $category, Request $request): Response
    {
        return $this->performDelete($category, $request);
    }

    protected function getEntityClass(): string
    {
        return $this->categoryClass;
    }

    protected function getFormClass(): string
    {
        return CategoryType::class;
    }

    protected function getNewTemplate(): string
    {
        return '@SymkitCrud/crud/entity_form.html.twig';
    }

    protected function getEditTemplate(): string
    {
        return '@SymkitCrud/crud/entity_form.html.twig';
    }

    protected function getRoutePrefix(): string
    {
        return 'admin_category';
    }

    protected function configureListFields(): array
    {
        return [
            'name' => [
                'label' => 'admin.list.name',
                'sortable' => true,
            ],
            'slug' => [
                'label' => 'admin.list.slug',
                'sortable' => true,
                'cell_class' => 'font-mono text-xs',
            ],
            'pages' => [
                'label' => 'admin.list.pages',
                'template' => '@SymkitCrud/crud/field/count.html.twig',
            ],
            'actions' => [
                'label' => '',
                'template' => '@SymkitCrud/crud/field/actions.html.twig',
                'edit_route' => 'admin_category_edit',
                'header_class' => 'text-right',
                'cell_class' => 'text-right',
            ],
        ];
    }

    protected function configureSearchFields(): array
    {
        return ['name', 'slug'];
    }
}
