<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Metadata;

use Exception;
use Symkit\MediaBundle\Service\MediaUrlGenerator;
use Symkit\MetadataBundle\Contract\JsonLdCollectorInterface;
use Symkit\MetadataBundle\Contract\JsonLdPopulatorInterface;
use Symkit\MetadataBundle\Contract\MetadataPopulatorInterface;
use Symkit\MetadataBundle\Contract\PageContextBuilderInterface;
use Symkit\MetadataBundle\JsonLd\Schema\FaqItem;
use Symkit\MetadataBundle\JsonLd\Schema\FaqSchema;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class MetadataPopulator implements MetadataPopulatorInterface, JsonLdPopulatorInterface
{
    /**
     * @param class-string $pageClass
     */
    public function __construct(
        private MediaUrlGenerator $mediaUrlGenerator,
        private UrlGeneratorInterface $urlGenerator,
        private string $pageClass = \Symkit\PageBundle\Entity\Page::class,
    ) {
    }

    public function supports(object $subject): bool
    {
        return $subject instanceof $this->pageClass;
    }

    /**
     * @param Page $subject
     */
    public function populateMetadata(object $subject, PageContextBuilderInterface $builder): void
    {
        $builder->setTitle($subject->getMetaTitle() ?? $subject->getTitle());

        if ($subject->getMetaDescription() || $subject->getExcerpt()) {
            $builder->setDescription($subject->getMetaDescription() ?? $subject->getExcerpt());
        }

        if ($subject->getOgImage()) {
            $url = $this->mediaUrlGenerator->generateUrl($subject->getOgImage());
            if (null !== $url) {
                $baseUrl = $this->urlGenerator->getContext()->getScheme() . '://' . $this->urlGenerator->getContext()->getHost();
                $builder->setOgImage($baseUrl . $url);
            }
        }
    }

    /**
     * @param Page $subject
     */
    public function populateJsonLd(object $subject, JsonLdCollectorInterface $collector): void
    {
        $faq = $subject->getFaq();
        if (null === $faq) {
            return;
        }

        $items = [];
        foreach ($faq->getFaqItems() as $item) {
            $question = $item->getQuestion();
            $answer = $item->getAnswer();
            if (null !== $question && null !== $answer) {
                $items[] = new FaqItem($question, $answer);
            }
        }

        if ($items !== []) {
            $collector->add(new FaqSchema($items));
        }
    }
}
