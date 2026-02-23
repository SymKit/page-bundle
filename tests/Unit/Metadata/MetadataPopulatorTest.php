<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Metadata;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symkit\MediaBundle\Service\MediaUrlGenerator;
use Symkit\MetadataBundle\Contract\PageContextBuilderInterface;
use Symkit\PageBundle\Entity\Page;
use Symkit\PageBundle\Metadata\MetadataPopulator;

final class MetadataPopulatorTest extends TestCase
{
    public function testSupportsReturnsTrueForPage(): void
    {
        $populator = new MetadataPopulator(
            $this->createMock(MediaUrlGenerator::class),
            $this->createMock(UrlGeneratorInterface::class),
            Page::class,
        );
        self::assertTrue($populator->supports(new Page()));
    }

    public function testSupportsReturnsFalseForOtherObject(): void
    {
        $populator = new MetadataPopulator(
            $this->createMock(MediaUrlGenerator::class),
            $this->createMock(UrlGeneratorInterface::class),
            Page::class,
        );
        self::assertFalse($populator->supports(new stdClass()));
    }

    public function testPopulateMetadataSetsTitleFromMetaTitleOrTitle(): void
    {
        $builder = $this->createMock(PageContextBuilderInterface::class);
        $builder->expects(self::once())->method('setTitle')->with('My Title');

        $page = new Page();
        $page->setTitle('My Title');

        $populator = new MetadataPopulator(
            $this->createMock(MediaUrlGenerator::class),
            $this->createMock(UrlGeneratorInterface::class),
            Page::class,
        );
        $populator->populateMetadata($page, $builder);
    }

    public function testPopulateMetadataUsesMetaTitleOverTitle(): void
    {
        $builder = $this->createMock(PageContextBuilderInterface::class);
        $builder->expects(self::once())->method('setTitle')->with('SEO Title');

        $page = new Page();
        $page->setTitle('Page Title');
        $page->setMetaTitle('SEO Title');

        $populator = new MetadataPopulator(
            $this->createMock(MediaUrlGenerator::class),
            $this->createMock(UrlGeneratorInterface::class),
            Page::class,
        );
        $populator->populateMetadata($page, $builder);
    }
}
