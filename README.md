[![CI](https://github.com/symkit/page-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/symkit/page-bundle/actions)
[![Latest Version](https://img.shields.io/packagist/v/symkit/page-bundle.svg)](https://packagist.org/packages/symkit/page-bundle)
[![PHPStan Level 9](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)

# Symkit Page Bundle

The **Symkit Page Bundle** provides a robust and flexible system for managing dynamic content pages and categories within your Symfony application. It integrates with the SymKit ecosystem: `symkit/menu-bundle`, `symkit/metadata-bundle`, `symkit/form-bundle`, `symkit/crud-bundle`, `symkit/faq-bundle`, `symkit/search-bundle`, and `symkit/sitemap-bundle`. Optional: `symkit/routing-bundle` for database-driven routes and `symkit/media-bundle` for OG images.

## Features

-   **Dynamic Page Management**: Create, edit, and publish pages with rich content.
-   **Categorization**: Organize pages into hierarchical categories.
-   **SEO Optimized**: Built-in support for meta titles, descriptions, and Open Graph images.
-   **Sitemap Integration**: Automatically generates sitemap entries for published pages.
-   **Menu Integration**: Link pages to specific menus and highlight active menu items automatically.
-   **Flexible Routing**: Custom slug management with conflict resolution.
-   **Admin Interface**: Ready-to-use CRUD controllers for Pages and Categories.

## Installation

### 1. Require the Bundle

Add the bundle to your project's `composer.json` (assuming it's a local package):

```json
"require": {
    "symkit/page-bundle": "@dev"
}
```

Then run:

```bash
composer update symkit/page-bundle
```

### 2. Register the Bundle

If not automatically registered by Flex, add it to `config/bundles.php`:

```php
return [
    // ...
    Symkit\PageBundle\SymkitPageBundle::class => ['all' => true],
];
```

### 3. Import Routes

Import the bundle routes in your application (e.g. `config/routes.yaml`):

```yaml
symkit_page:
    resource: '@SymkitPageBundle/config/routes.yaml'
    prefix: /
```

You can change the prefix or omit it if you use a different structure.

### 4. Database Migration

The bundle includes `Page` and `Category` entities. Update your database schema:

```bash
php bin/console make-migration
php bin/console doctrine:migrations:migrate
```

## Configuration

The bundle is fully configurable. All features can be enabled or disabled, and entity/repository classes can be overridden.

### Reference

```yaml
# config/packages/symkit_page.yaml
symkit_page:
    base_layout: "base.html.twig"

    # Override entity and repository classes (e.g. for custom fields or behavior)
    entity:
        page_class: Symkit\PageBundle\Entity\Page
        page_repository_class: Symkit\PageBundle\Repository\PageRepository
        category_class: Symkit\PageBundle\Entity\Category
        category_repository_class: Symkit\PageBundle\Repository\CategoryRepository

    # Enable/disable features
    front:
        enabled: true
        route_path: "/{slug}"
        route_name: page_show
        controller: Symkit\PageBundle\Controller\PageController
    admin:
        enabled: true
    sitemap:
        enabled: true
    search:
        enabled: true
    metadata:
        enabled: true

    layouts:
        simple:
            label: "Layout Simple"
            path: "@SymkitPage/layout/simple.html.twig"
        # ...
```

-   **entity**: Replace with your own entity/repository classes when extending the bundle's models.
-   **front / admin / sitemap / search / metadata**: Set `enabled: false` to disable the corresponding controllers, sitemap loader, search provider, or metadata (breadcrumbs, SEO populator).

### Layouts

Pages can use different layouts. You define them in your application configuration:

```yaml
symkit_page:
    base_layout: "website/layout/base.html.twig"
    layouts:
        default:
            label: "Layout Standard"
            path: "@SymkitPage/layout/content.html.twig"
        simple:
            label: "Layout Simple"
            path: "website/layout/simple.html.twig"
        with_toc:
            label: "Layout with Table of Contents"
            path: "website/layout/with_toc.html.twig"
```

#### Layout Inheritance (Bridge Pattern)

The bundle uses a "Bridge" architecture to remain generic while injecting bundle-specific logic (like TOC automation):

1.  **`PageController`** renders `@SymkitPage/layout/bridge.html.twig`.
2.  **`bridge.html.twig`** extends the **`layout`** variable (chosen in admin).
3.  Your project **Layouts** (simple, with_toc, etc.) should extend the **`base_layout`** global variable.

This ensures that regardless of the layout chosen, the bundle can inject its Stimulus controllers and necessary attributes without hardcoding paths to your project templates.

### Translations

All user-facing strings use the **SymkitPageBundle** translation domain. XLIFF files are shipped in the bundle's `translations/` directory for **en** and **fr**. Override or extend them in your app:

-   Copy keys into your `translations/SymkitPageBundle.en.xlf` (and `.fr.xlf`) to customize.
-   Or replace the bundle's translations by placing files in `translations/` with the same domain.

Constraint messages (e.g. `validation.unique_route`, `validation.slug_pattern`) use the same keys; ensure your validator translation domain includes them or override in `validators.*.xlf` if needed.

### Dependencies

Suggested bundles (see `composer.json` suggest):

-   `symkit/crud-bundle`: Admin CRUD interface.
-   `symkit/menu-bundle`: Menu integration and active item highlight.
-   `symkit/metadata-bundle`: SEO, JSON-LD and breadcrumbs.
-   `symkit/form-bundle`: SlugType, FormSectionType and form theme.
-   `symkit/faq-bundle`: FAQ blocks on pages.
-   `symkit/search-bundle`: Global search (admin).
-   `symkit/sitemap-bundle`: XML sitemap for pages.
-   `symkit/routing-bundle`: Database-driven routes for pages (load routes with `type: database` in the host app).
-   `symkit/media-bundle`: OG images and media picker (use the `|media_url` Twig filter in templates).

## Usage

### Managing Pages

Access the admin interface (typically at `/admin/pages`) to manage pages.

**Fields:**
-   **Title & Slug**: define the page identity and URL.
-   **Content**: Rich text content.
-   **Status**: Draft or Published.
-   **Category**: Group pages logically.
-   **SEO**: Override meta title/description and set an OG Image.
-   **Menu Integration**:
    -   *Sidebar Left Menu*: Override the default sidebar for this specific page.
    -   *Active Menu*: Specify which menu should be considered "active" when viewing this page.

### Managing Categories

Categories allow you to group pages and define default behaviors (admin at `/admin/categories`).

**Fields:**
-   **Name & Slug**: Identifier.
-   **Default Sidebar Menu**: Set a default menu that will be displayed on the sidebar for all pages in this category (unless overridden by the page itself).

### Frontend Rendering

To render a page, use the `PageController`; the route `page_show` is provided by the bundle (path `/{slug}` by default). Import the bundle routes as shown in Installation.

You can customize the templates by overriding them in `templates/bundles/SymkitPageBundle/`.

**Default Template Structure:**
-   `@SymkitPage/layout/bridge.html.twig`: Main entry point (Bridge) that extends the selected layout.

## Architecture & Services

-   **MetadataPopulator**: Implements SymKit metadata-bundle populators: SEO (title, description, OG image) and JSON-LD FAQ schema when a page is viewed.
-   **PageBreadcrumbBuilder**: Builds breadcrumbs from the page (Home, active menu path, current page) when the page is in request attributes.
-   **PageSitemapLoader**: Feeds the sitemap generation process with published pages.
-   **PageRepository**: Provides helper methods for fetching published pages.

## Development

-   **Tests**: `vendor/bin/phpunit` (Unit tests by default; integration tests are in the `integration` group).
-   **Code style**: `make cs-fix` (requires `friendsofphp/php-cs-fixer` in require-dev).
-   **Static analysis**: `make phpstan` (requires `phpstan/phpstan` in require-dev).
-   **Full pipeline**: `make quality` runs cs-check, phpstan, lint and test.

## License

Private / Proprietary.
