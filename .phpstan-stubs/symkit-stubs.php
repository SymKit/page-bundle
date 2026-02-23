<?php

declare(strict_types=1);

/**
 * Stubs for optional Symkit packages (suggested deps) so PHPStan can resolve symbols.
 * These are not real implementations.
 */

namespace Symkit\CrudBundle\Controller {

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    abstract class AbstractCrudController
    {
        abstract protected function getEntityClass(): string;

        abstract protected function getFormClass(): string;

        abstract protected function getRoutePrefix(): string;

        protected function getNewTemplate(): string
        {
            return '';
        }

        protected function getEditTemplate(): string
        {
            return '';
        }

        /**
         * @return array<string, mixed>
         */
        protected function configureListFields(): array
        {
            return [];
        }

        /**
         * @return array<int, string>
         */
        protected function configureSearchFields(): array
        {
            return [];
        }

        /**
         * @param array<string, mixed> $context
         */
        protected function renderIndex(Request $request, array $context = []): Response
        {
            return new Response();
        }

        /**
         * @param array<string, mixed> $context
         */
        protected function renderNew(object $entity, Request $request, array $context = []): Response
        {
            return new Response();
        }

        /**
         * @param array<string, mixed> $context
         */
        protected function renderEdit(object $entity, Request $request, array $context = []): Response
        {
            return new Response();
        }

        protected function performDelete(object $entity, Request $request): Response
        {
            return new Response();
        }
    }
}

namespace Symkit\MetadataBundle\Attribute {

    use Attribute;

    #[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
    class Seo
    {
        public function __construct(
            public ?string $title = null,
            public ?string $description = null,
        ) {
        }
    }

    #[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
    class Breadcrumb
    {
        /**
         * @param array<int, array<string, mixed>> $items
         */
        public function __construct(
            public string $context = 'admin',
            public array $items = [],
        ) {
        }
    }
}

namespace Symkit\MenuBundle\Attribute {

    use Attribute;

    #[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
    class ActiveMenu
    {
        public function __construct(
            public string $context = 'admin',
            public string $code = '',
        ) {
        }
    }
}

namespace Symkit\MenuBundle\Manager {

    class MenuManager
    {
        public function setActiveId(string $menuCode, string $itemId): void
        {
        }
    }
}

namespace Symkit\MenuBundle\Entity {

    class Menu
    {
        public function getCode(): string
        {
            return '';
        }

        public function getId(): int
        {
            return 0;
        }
    }

    class MenuItem
    {
        public function getIdentifier(): string
        {
            return '';
        }

        public function getId(): int
        {
            return 0;
        }

        public function getUrl(): ?string
        {
            return null;
        }

        public function getRoute(): ?object
        {
            return null;
        }

        public function getLabel(): string
        {
            return '';
        }

        public function getParent(): ?self
        {
            return null;
        }
    }
}

namespace Symkit\RoutingBundle\Entity {

    class Route
    {
        public function getPath(): string
        {
            return '';
        }

        public function getController(): ?string
        {
            return null;
        }

        public function setController(string $controller): void
        {
        }

        /**
         * @param array<int, string> $methods
         */
        public function setMethods(array $methods): void
        {
        }

        public function setIsActive(bool $active): void
        {
        }

        public function setPath(?string $path): void
        {
        }

        public function setName(string $name): void
        {
        }

        /**
         * @param array<string, mixed> $defaults
         */
        public function setDefaults(array $defaults): void
        {
        }

        public function getName(): string
        {
            return '';
        }

        public function isExcludeFromSitemap(): bool
        {
            return false;
        }

        public function getSitemapPriority(): ?string
        {
            return null;
        }
    }
}

namespace Symkit\MediaBundle\Entity {

    class Media
    {
        public function getAltText(): ?string
        {
            return null;
        }
    }
}

namespace Symkit\FaqBundle\Entity {

    class Faq
    {
    }
}

namespace Symkit\MediaBundle\Form {

    class MediaType
    {
    }
}

namespace Symkit\MediaBundle\Service {

    class MediaUrlGenerator
    {
        public function generate(object $media): string
        {
            return '';
        }

        public function generateUrl(object $media): string
        {
            return '';
        }
    }
}

namespace Symkit\MetadataBundle\Contract {

    interface BreadcrumbBuilderInterface
    {
        public function build(BreadcrumbServiceInterface $service): void;

        public function isRootRoute(string $route): bool;
    }

    interface BreadcrumbServiceInterface
    {
        public function add(string $label, string $url): void;
    }

    interface MetadataPopulatorInterface
    {
        public function populateMetadata(object $subject, PageContextBuilderInterface $builder): void;
    }

    interface JsonLdPopulatorInterface
    {
        public function populateJsonLd(object $subject, JsonLdCollectorInterface $collector): void;
    }

    interface JsonLdCollectorInterface
    {
        public function add(object $schema): void;
    }

    interface PageContextBuilderInterface
    {
        public function setTitle(?string $title): void;

        public function setDescription(?string $description): void;

        public function setOgImage(?string $url): void;
    }
}

namespace Symkit\MetadataBundle\JsonLd\Schema {

    class FaqItem
    {
        public function __construct(
            public string $question = '',
            public string $answer = '',
        ) {
        }
    }

    class FaqSchema
    {
        /**
         * @param array<int, FaqItem> $items
         */
        public function __construct(
            public array $items = [],
        ) {
        }
    }
}

namespace Symkit\SearchBundle\Contract {

    interface SearchProviderInterface
    {
        /**
         * @return iterable<object>
         */
        public function search(string $query): iterable;

        public function getCategory(): string;

        public function getPriority(): int;
    }
}

namespace Symkit\SearchBundle\Model {

    class SearchResult
    {
        public function __construct(
            public string $title = '',
            public string $subtitle = '',
            public string $url = '',
            public string $icon = '',
            public ?string $badge = null,
        ) {
        }
    }
}

namespace Symkit\SitemapBundle\Contract {

    use Symkit\SitemapBundle\Model\SitemapUrl;

    interface SitemapLoaderInterface
    {
        /**
         * @return \Traversable<SitemapUrl>|array<SitemapUrl>
         */
        public function load(int $limit, int $offset): iterable;
    }
}

namespace Symkit\FormBundle\Form\Type {

    class FormSectionType
    {
    }

    class SlugType
    {
    }
}

namespace Symkit\SitemapBundle\Model {

    class SitemapUrl
    {
        /**
         * @param \DateTimeInterface|string|null $lastmod
         * @param array<int, array<string, mixed>>|null $images
         */
        public function __construct(
            public string $loc = '',
            public \DateTimeInterface|string|null $lastmod = null,
            public ?string $changefreq = null,
            public ?string $priority = null,
            public ?array $images = null,
        ) {
        }
    }
}
