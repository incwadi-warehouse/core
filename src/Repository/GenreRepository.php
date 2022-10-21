<?php

namespace App\Repository;

use Doctrine\Common\Collections\Criteria;
use App\Entity\Branch;
use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    public function findDemanded(Branch $branch): array
    {
        // @deprecated getOrderBy()
        if ('books' === $branch->getOrderBy()) {
            return $this->findByBranchAndOrderByBooks(
                $branch
            );
        }

        return $this->findByBranch(
            $branch,
            ['name' => 'ASC']
        );
    }

    // @deprecated
    public function findByBranchAndOrderByBooks(Branch $branch): array
    {
        return $this->createQueryBuilder('g')
            ->addSelect('SIZE(g.books) AS HIDDEN books')
            ->andWhere('g.branch = :branch')
            ->setParameter('branch', $branch)
            ->orderBy('books', Criteria::DESC)
            ->getQuery()
            ->getResult()
        ;
    }
}
