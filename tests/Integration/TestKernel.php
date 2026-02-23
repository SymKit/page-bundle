<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    /** @var list<class-string> */
    private array $testBundles = [];

    /** @var callable(ContainerBuilder): void|null */
    private $testConfig;

    public function registerBundles(): array
    {
        $bundles = [
            new FrameworkBundle(),
            new DoctrineBundle(),
        ];

        foreach ($this->testBundles as $class) {
            $bundles[] = new $class();
        }

        return $bundles;
    }

    /**
     * @param class-string $class
     */
    public function addTestBundle(string $class): void
    {
        $this->testBundles[] = $class;
    }

    public function addTestConfig(callable $config): void
    {
        $this->testConfig = $config;
    }

    public function handleOptions(array $options): void
    {
        // Reserved for future options
    }

    private function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'secret' => 'test',
            'router' => ['resource' => 'kernel::loadRoutes', 'type' => 'service'],
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'url' => 'sqlite:///:memory:',
            ],
            'orm' => [
                'auto_mapping' => true,
            ],
        ]);

        if (null !== $this->testConfig) {
            ($this->testConfig)($container);
        }
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        // No routes needed for boot test
    }
}
