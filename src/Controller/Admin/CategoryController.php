<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Controller\Admin;

use Symkit\CrudBundle\Controller\AbstractCrudController;
use Symkit\MenuBundle\Attribute\ActiveMenu;
use Symkit\MetadataBundle\Attribute\Breadcrumb;
use Symkit\MetadataBundle\Attribute\Seo;
use Symkit\PageBundle\Entity\Category;
use Symkit\PageBundle\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CategoryController extends AbstractCrudController
{
    /**
     * @param class-string $categoryClass
     */
    public function __construct(
        private readonly string $categoryClass,
    ) {
    }

    #[Seo(title: 'Category Management', description: 'Manage page categories.')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'Categories', 'route' => 'admin_category_list']])]
    #[ActiveMenu('admin', 'categories')]
    public function list(Request $request): Response
    {
        return $this->renderIndex($request, [
            'page_title' => 'Category Management',
        ]);
    }

    #[Seo(title: 'Create Category')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'Categories', 'route' => 'admin_category_list']])]
    #[ActiveMenu('admin', 'categories')]
    public function create(Request $request): Response
    {
        return $this->renderNew(new $this->categoryClass(), $request, [
            'page_title' => 'Create Category',
        ]);
    }

    #[Seo(title: 'Edit Category')]
    #[Breadcrumb(context: 'admin', items: [['label' => 'Categories', 'route' => 'admin_category_list']])]
    #[ActiveMenu('admin', 'categories')]
    public function edit(Category $category, Request $request): Response
    {
        return $this->renderEdit($category, $request, [
            'page_title' => 'Edit Category',
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
