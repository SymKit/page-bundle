<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Symkit\PageBundle\Entity\Page;

final class PageTest extends TestCase
{
    public function testStatusConstants(): void
    {
        self::assertSame('draft', Page::STATUS_DRAFT);
        self::assertSame('published', Page::STATUS_PUBLISHED);
    }
}
