<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    /**
     * @var int
     */
    public const LIMIT = 100;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function findDemanded(string $term)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a');

        $name = $qb->expr()->concat(
            'a.firstname',
            $qb->expr()->concat(
                $qb->expr()->literal(' '),
                'a.surname'
            )
        );
        $nameReverse = $qb->expr()->concat(
            'a.surname',
            $qb->expr()->concat(
                $qb->expr()->literal(' '),
                'a.firstname'
            )
        );
        $nameCommaAndSpace = $qb->expr()->concat(
            'a.surname',
            $qb->expr()->concat(
                $qb->expr()->literal(', '),
                'a.firstname'
            )
        );
        $name4Comma = $qb->expr()->concat(
            'a.surname',
            $qb->expr()->concat(
                $qb->expr()->literal(','),
                'a.firstname'
            )
        );
        $qb->from('App:Author', 'a');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->like('a.firstname', ':term'),
                $qb->expr()->like('a.surname', ':term'),
                $qb->expr()->like($name, ':term'),
                $qb->expr()->like($nameReverse, ':term'),
                $qb->expr()->like($nameCommaAndSpace, ':term'),
                $qb->expr()->like($name4Comma, ':term')
            )
        );

        $term = preg_replace('#[%\*]#', '', $term);
        $qb->setParameter('term', '%'.$term.'%');

        $qb->setMaxResults(self::LIMIT);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
