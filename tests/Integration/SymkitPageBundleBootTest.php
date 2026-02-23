<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Integration;

use Symkit\PageBundle\Service\PageLayoutRegistry;
use Symkit\PageBundle\SymkitPageBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Integration tests require Doctrine and full container.
 * Run with: phpunit --testsuite Unit
 */
final class SymkitPageBundleBootTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \restore_exception_handler();
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * @param array<string, mixed> $options
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        $kernel = parent::createKernel($options);
        \assert($kernel instanceof TestKernel);
        $kernel->addTestBundle(SymkitPageBundle::class);
        $kernel->addTestConfig(static function (\Symfony\Component\DependencyInjection\ContainerBuilder $container): void {
            $container->loadFromExtension('framework', [
                'test' => true,
                'translator' => ['default_path' => '%kernel.project_dir%/translations'],
            ]);
        });
        $kernel->handleOptions($options);

        return $kernel;
    }

    /**
     * @group integration
     */
    public function testBundleBoots(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        self::assertTrue($container->hasParameter('symkit_page.layouts'));
        self::assertTrue($container->hasParameter('symkit_page.entity.page_class'));
        self::assertTrue($container->hasParameter('symkit_page.front.enabled'));
    }

    /**
     * @group integration
     */
    public function testPageLayoutRegistryIsRegistered(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        self::assertTrue($container->has(PageLayoutRegistry::class));
        $registry = $container->get(PageLayoutRegistry::class);
        self::assertInstanceOf(PageLayoutRegistry::class, $registry);
        $layouts = $registry->getLayouts();
        self::assertArrayHasKey('simple', $layouts);
    }
}

