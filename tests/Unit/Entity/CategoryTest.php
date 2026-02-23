<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Symkit\PageBundle\Entity\Category;

final class CategoryTest extends TestCase
{
    public function testGetNameReturnsNullByDefault(): void
    {
        $category = new Category();
        self::assertNull($category->getName());
    }

    public function testSetNameAndGetName(): void
    {
        $category = new Category();
        $category->setName('Test Category');
        self::assertSame('Test Category', $category->getName());
    }

    public function testToStringReturnsName(): void
    {
        $category = new Category();
        $category->setName('My Category');
        self::assertSame('My Category', $category->__toString());
    }

    public function testToStringReturnsEmptyStringWhenNameIsNull(): void
    {
        $category = new Category();
        self::assertSame('', $category->__toString());
    }
}
