<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Form;

use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\PageBundle\Entity\Category;
use Symkit\PageBundle\Form\CategoryType;

final class CategoryTypeTest extends TestCase
{
    public function testConfigureOptionsSetsDataClassAndTranslationDomain(): void
    {
        $resolver = new OptionsResolver();
        $type = new CategoryType(Category::class);

        $type->configureOptions($resolver);

        $options = $resolver->resolve();
        self::assertSame(Category::class, $options['data_class']);
        self::assertSame('SymkitPageBundle', $options['translation_domain']);
    }
}
