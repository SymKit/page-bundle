<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symkit\MediaBundle\Entity\Media;
use Symkit\PageBundle\Entity\Category;
use Symkit\PageBundle\Entity\Page;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
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
        return $this->createQueryBuilder('p')
            ->innerJoin('p.route', 'r')
            ->where('r.path = :path')
            ->setParameter('path', '/'.$slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
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

        return $qb->getQuery()->getResult();
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
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', $status)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByMedia(Media $media): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.ogImage = :media')
            ->setParameter('media', $media)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Page[]
     */
    public function findByCategory(Category $category): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return iterable<Page>
     */
    public function findForGlobalSearch(string $query, int $limit = 5): iterable
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.route', 'r')
            ->addSelect('r')
            ->where('p.title LIKE :query OR p.content LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($limit)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->toIterable()
        ;
    }
}
