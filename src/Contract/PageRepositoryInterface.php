<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Contract;

use Symkit\PageBundle\Entity\Page;

interface PageRepositoryInterface
{
    public function countPublished(): int;

    /**
     * @return Page[]
     */
    public function findPublished(?int $limit = null, ?int $offset = null): array;

    /**
     * @return iterable<Page>
     */
    public function findForGlobalSearch(string $query, int $limit = 5): iterable;
}
