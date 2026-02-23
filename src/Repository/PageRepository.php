<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symkit\MediaBundle\Entity\Media;
use Symkit\PageBundle\Contract\PageRepositoryInterface;
use Symkit\PageBundle\Entity\Category;
use Symkit\PageBundle\Entity\Page;

/**
 * @extends ServiceEntityRepository<Page>
 */
final class PageRepository extends ServiceEntityRepository implements PageRepositoryInterface
{
    /**
     * @param class-string<Page> $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass = Page::class)
    {
        parent::__construct($registry, $entityClass);
    }

    public function findBySlug(string $slug): ?Page
    {
        /** @var Page|null $result */
        $result = $this->createQueryBuilder('p')
            ->innerJoin('p.route', 'r')
            ->where('r.path = :path')
            ->setParameter('path', '/'.$slug)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @return Page[]
     */
    public function findPublished(?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', Page::STATUS_PUBLISHED)
            ->orderBy('p.updatedAt', 'DESC')
        ;

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        /** @var Page[] $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function countPublished(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.status = :status')
            ->setParameter('status', Page::STATUS_PUBLISHED)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return Page[]
     */
    /**
     * @return Page[]
     */
    public function findByStatus(string $status): array
    {
        /** @var Page[] $result */
        $result = $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', $status)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return Page[]
     */
    public function findByMedia(Media $media): array
    {
        /** @var Page[] $result */
        $result = $this->createQueryBuilder('p')
            ->where('p.ogImage = :media')
            ->setParameter('media', $media)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return Page[]
     */
    /**
     * @return Page[]
     */
    public function findByCategory(Category $category): array
    {
        /** @var Page[] $result */
        $result = $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return iterable<Page>
     */
    /**
     * @return iterable<Page>
     */
    public function findForGlobalSearch(string $query, int $limit = 5): iterable
    {
        /** @var iterable<Page> $result */
        $result = $this->createQueryBuilder('p')
            ->leftJoin('p.route', 'r')
            ->addSelect('r')
            ->where('p.title LIKE :query OR p.content LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($limit)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->toIterable();

        return $result;
    }
}
