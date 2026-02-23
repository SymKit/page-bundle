<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Service;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\PageBundle\Service\PageLayoutRegistry;

final class PageLayoutRegistryTest extends TestCase
{
    private const LAYOUTS = [
        'simple' => [
            'label' => 'layout.simple',
            'path' => '@SymkitPage/layout/simple.html.twig',
        ],
        'doc' => [
            'label' => 'layout.doc',
            'path' => '@SymkitPage/layout/content.html.twig',
        ],
    ];

    public function testGetLayoutsReturnsConfiguredLayouts(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $registry = new PageLayoutRegistry(self::LAYOUTS, $translator);

        self::assertSame(self::LAYOUTS, $registry->getLayouts());
    }

    public function testGetLayoutChoicesTranslatesLabels(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
            ->willReturnMap([
                ['layout.simple', [], 'SymkitPageBundle', null, 'Simple'],
                ['layout.doc', [], 'SymkitPageBundle', null, 'Document'],
            ]);
        $registry = new PageLayoutRegistry(self::LAYOUTS, $translator);

        $choices = $registry->getLayoutChoices();

        self::assertSame('simple', $choices['Simple']);
        self::assertSame('doc', $choices['Document']);
    }

    public function testGetLayoutPathReturnsPathForExistingLayout(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $registry = new PageLayoutRegistry(self::LAYOUTS, $translator);

        self::assertSame('@SymkitPage/layout/simple.html.twig', $registry->getLayoutPath('simple'));
        self::assertSame('@SymkitPage/layout/content.html.twig', $registry->getLayoutPath('doc'));
    }

    public function testGetLayoutPathWithNullUsesSimple(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $registry = new PageLayoutRegistry(self::LAYOUTS, $translator);

        self::assertSame('@SymkitPage/layout/simple.html.twig', $registry->getLayoutPath(null));
    }

    public function testGetLayoutPathWithUnknownLayoutThrows(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $registry = new PageLayoutRegistry(self::LAYOUTS, $translator);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Layout "unknown" not found');

        $registry->getLayoutPath('unknown');
    }
}
