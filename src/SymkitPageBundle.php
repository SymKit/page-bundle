<?php

declare(strict_types=1);

namespace Symkit\PageBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symkit\PageBundle\Controller\Admin\CategoryController;
use Symkit\PageBundle\Controller\Admin\PageController as AdminPageController;
use Symkit\PageBundle\Controller\PageController as FrontPageController;
use Symkit\PageBundle\Entity\Category;
use Symkit\PageBundle\Entity\Page;
use Symkit\PageBundle\EventListener\PageRouteControllerListener;
use Symkit\PageBundle\Form\CategoryType;
use Symkit\PageBundle\Form\PageType;
use Symkit\PageBundle\Metadata\MetadataPopulator;
use Symkit\PageBundle\Metadata\PageBreadcrumbBuilder;
use Symkit\PageBundle\Repository\CategoryRepository;
use Symkit\PageBundle\Repository\PageRepository;
use Symkit\PageBundle\Search\PageSearchProvider;
use Symkit\PageBundle\Service\PageLayoutRegistry;
use Symkit\PageBundle\Sitemap\PageSitemapLoader;

class SymkitPageBundle extends AbstractBundle
{
    protected string $extensionAlias = 'symkit_page';

    public function configure(DefinitionConfigurator $definition): void
    {
        $root = $definition->rootNode();

        $root
            ->children()
                ->scalarNode('base_layout')->defaultValue('base.html.twig')->end()
                ->arrayNode('layouts')
                    ->defaultValue([
                        'simple' => [
                            'label' => 'layout.simple',
                            'path' => '@SymkitPage/layout/simple.html.twig',
                        ],
                        'doc' => [
                            'label' => 'layout.doc',
                            'path' => '@SymkitPage/layout/content.html.twig',
                        ],
                        'with_toc' => [
                            'label' => 'layout.with_toc',
                            'path' => '@SymkitPage/layout/with_toc.html.twig',
                        ],
                        'hero' => [
                            'label' => 'layout.hero',
                            'path' => '@SymkitPage/layout/hero.html.twig',
                        ],
                    ])
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->isRequired()->end()
                            ->scalarNode('path')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('entity')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('page_class')->defaultValue(Page::class)->end()
                        ->scalarNode('page_repository_class')->defaultValue(PageRepository::class)->end()
                        ->scalarNode('category_class')->defaultValue(Category::class)->end()
                        ->scalarNode('category_repository_class')->defaultValue(CategoryRepository::class)->end()
                    ->end()
                ->end()
                ->arrayNode('front')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('route_path')->defaultValue('/{slug}')->end()
                        ->scalarNode('route_name')->defaultValue('page_show')->end()
                        ->scalarNode('controller')->defaultValue(FrontPageController::class)->end()
                    ->end()
                ->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('sitemap')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('search')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('metadata')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array{
     *     base_layout: string,
     *     layouts: array<string, array{label: string, path: string}>,
     *     entity: array{page_class: string, page_repository_class: string, category_class: string, category_repository_class: string},
     *     front: array{enabled: bool, route_path: string, route_name: string, controller: string},
     *     admin: array{enabled: bool},
     *     sitemap: array{enabled: bool},
     *     search: array{enabled: bool},
     *     metadata: array{enabled: bool},
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('symkit_page.layouts', $config['layouts'])
            ->set('symkit_page.entity.page_class', $config['entity']['page_class'])
            ->set('symkit_page.entity.page_repository_class', $config['entity']['page_repository_class'])
            ->set('symkit_page.entity.category_class', $config['entity']['category_class'])
            ->set('symkit_page.entity.category_repository_class', $config['entity']['category_repository_class'])
            ->set('symkit_page.front.enabled', $config['front']['enabled'])
            ->set('symkit_page.front.controller', $config['front']['controller'])
            ->set('symkit_page.front.route_name', $config['front']['route_name'])
            ->set('symkit_page.admin.enabled', $config['admin']['enabled'])
            ->set('symkit_page.sitemap.enabled', $config['sitemap']['enabled'])
            ->set('symkit_page.search.enabled', $config['search']['enabled'])
            ->set('symkit_page.metadata.enabled', $config['metadata']['enabled']);

        $services = $container->services();

        $services->set(PageLayoutRegistry::class)
            ->arg('$layouts', '%symkit_page.layouts%')
            ->arg('$translator', new Reference('translator'));

        $services->set($config['entity']['page_repository_class'])
            ->arg('$entityClass', '%symkit_page.entity.page_class%');
        $services->alias(PageRepository::class, $config['entity']['page_repository_class']);

        $services->set($config['entity']['category_repository_class'])
            ->arg('$entityClass', '%symkit_page.entity.category_class%');
        $services->alias(CategoryRepository::class, $config['entity']['category_repository_class']);

        $services->set(PageType::class)
            ->arg('$pageClass', '%symkit_page.entity.page_class%')
            ->arg('$categoryClass', '%symkit_page.entity.category_class%');
        $services->set(CategoryType::class)
            ->arg('$categoryClass', '%symkit_page.entity.category_class%');

        if ($config['front']['enabled']) {
            $services->set(FrontPageController::class)
                ->tag('controller.service_arguments');

            $services->set(PageRouteControllerListener::class)
                ->arg('$pageClass', '%symkit_page.entity.page_class%')
                ->arg('$frontController', '%symkit_page.front.controller%')
                ->tag('doctrine.event_listener', ['event' => 'prePersist'])
                ->tag('doctrine.event_listener', ['event' => 'preUpdate']);
        }

        if ($config['admin']['enabled']) {
            $services->set(AdminPageController::class)
                ->arg('$pageClass', '%symkit_page.entity.page_class%')
                ->arg('$translator', new Reference('translator'))
                ->tag('controller.service_arguments');
            $services->set(CategoryController::class)
                ->arg('$categoryClass', '%symkit_page.entity.category_class%')
                ->arg('$translator', new Reference('translator'))
                ->tag('controller.service_arguments');
        }

        if ($config['sitemap']['enabled']) {
            $services->set(PageSitemapLoader::class)
                ->tag('symkit_sitemap.loader', ['index' => 'page']);
        }

        if ($config['metadata']['enabled']) {
            $services->set(MetadataPopulator::class)
                ->arg('$pageClass', '%symkit_page.entity.page_class%');
            $services->set(PageBreadcrumbBuilder::class)
                ->arg('$pageClass', '%symkit_page.entity.page_class%')
                ->arg('$pageRouteName', '%symkit_page.front.route_name%')
                ->tag('symkit_metadata.breadcrumb_builder', ['index' => 'website']);
        }

        if ($config['search']['enabled']) {
            $services->set(PageSearchProvider::class)
                ->tag('symkit_search.provider', ['engine' => 'admin']);
        }
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $configs = $builder->getExtensionConfig($this->extensionAlias);
        $baseLayout = $configs[0]['base_layout'] ?? 'base.html.twig';
        $bundlePath = $this->getPath();

        $builder->prependExtensionConfig('twig', [
            'paths' => [
                $bundlePath.'/templates' => 'SymkitPage',
            ],
            'globals' => [
                'base_layout' => $baseLayout,
            ],
        ]);

        $builder->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    $bundlePath.'/assets/controllers' => 'page',
                ],
            ],
        ]);
    }
}
