<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Form;

use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\PageBundle\Entity\Category;
use Symkit\PageBundle\Entity\Page;
use Symkit\PageBundle\Form\PageType;
use Symkit\PageBundle\Service\PageLayoutRegistry;

final class PageTypeTest extends TestCase
{
    public function testConfigureOptionsSetsDataClassAndTranslationDomain(): void
    {
        $resolver = new OptionsResolver();
        $translator = $this->createMock(\Symfony\Contracts\Translation\TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);
        $registry = new PageLayoutRegistry(['simple' => ['label' => 'layout.simple', 'path' => 'simple.html.twig']], $translator);
        $type = new PageType($registry, Page::class, Category::class);

        $type->configureOptions($resolver);

        $options = $resolver->resolve();
        self::assertSame(Page::class, $options['data_class']);
        self::assertSame('SymkitPageBundle', $options['translation_domain']);
    }
}
