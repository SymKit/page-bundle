<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symkit\FaqBundle\Entity\Faq;
use Symkit\MediaBundle\Entity\Media;
use Symkit\MenuBundle\Entity\Menu;
use Symkit\MenuBundle\Entity\MenuItem;
use Symkit\PageBundle\Repository\PageRepository;
use Symkit\RoutingBundle\Entity\Route;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['route'], message: 'validation.unique_route', errorPath: 'slug', groups: ['edit', 'create'])]
class Page
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['edit', 'create'])]
    #[Assert\Length(max: 255, groups: ['edit', 'create'])]
    private ?string $title = null;

    /**
     * @var string|null
     *                  Virtual property to hold the slug (from Route path)
     */
    private ?string $slug = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(groups: ['edit', 'create'])]
    private ?string $content = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $excerpt = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(groups: ['edit', 'create'])]
    #[Assert\Choice(choices: [self::STATUS_DRAFT, self::STATUS_PUBLISHED], groups: ['edit', 'create'])]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $template = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'pages')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'RESTRICT')]
    private ?Category $category = null;

    #[ORM\OneToOne(targetEntity: Route::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[Assert\Valid(groups: ['edit', 'create'])]
    private ?Route $route = null;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Menu $sidebarLeftMenu = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Media $ogImage = null;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Menu $activeMenu = null;

    #[ORM\ManyToOne(targetEntity: MenuItem::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?MenuItem $activeMenuItem = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Faq::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Faq $faq = null;

    public function __construct()
    {
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->ensureRouteExists();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
        $this->ensureRouteExists();
    }

    private function ensureRouteExists(): void
    {
        if (null === $this->route) {
            $this->route = new Route();
            $this->route->setMethods(['GET']);
            $this->route->setIsActive($this->isPublished());

            if ($this->slug) {
                $this->route->setPath('/'.$this->slug);
                $this->route->setName('page_'.$this->slug);
                $this->route->setDefaults(['slug' => $this->slug]);
            }
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        if (null === $this->slug && null !== $this->route) {
            $path = $this->route->getPath();

            return '' !== $path ? mb_ltrim($path, '/') : '';
        }

        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        if (null !== $this->route) {
            $this->route->setPath($slug ? '/'.$slug : null);
        }

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): static
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        if (null !== $this->route) {
            $this->route->setIsActive($this->isPublished());
        }

        return $this;
    }

    public function isPublished(): bool
    {
        return self::STATUS_PUBLISHED === $this->status;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setRoute(?Route $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getSidebarLeftMenu(): ?Menu
    {
        return $this->sidebarLeftMenu;
    }

    public function setSidebarLeftMenu(?Menu $sidebarLeftMenu): static
    {
        $this->sidebarLeftMenu = $sidebarLeftMenu;

        return $this;
    }

    public function getOgImage(): ?Media
    {
        return $this->ogImage;
    }

    public function setOgImage(?Media $ogImage): static
    {
        $this->ogImage = $ogImage;

        return $this;
    }

    public function getActiveMenu(): ?Menu
    {
        return $this->activeMenu;
    }

    public function setActiveMenu(?Menu $activeMenu): static
    {
        $this->activeMenu = $activeMenu;

        return $this;
    }

    public function getActiveMenuItem(): ?MenuItem
    {
        return $this->activeMenuItem;
    }

    public function setActiveMenuItem(?MenuItem $activeMenuItem): static
    {
        $this->activeMenuItem = $activeMenuItem;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): static
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): static
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getFaq(): ?Faq
    {
        return $this->faq;
    }

    public function setFaq(?Faq $faq): static
    {
        $this->faq = $faq;

        return $this;
    }

    /**
     * Get the effective sidebar menu (page-specific or category default).
     */
    public function getEffectiveSidebarMenu(): ?Menu
    {
        return $this->sidebarLeftMenu ?? $this->category?->getDefaultMenu();
    }

    public function __toString(): string
    {
        return (string) $this->getTitle();
    }
}
