<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Incwadi\Core\Entity\Author;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
    const LIMIT = 100;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function findDemanded(string $term)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a');
        $name = $qb->expr()->concat(
            'a.firstname',
            $qb->expr()->concat($qb->expr()->literal(' '),
            'a.surname')
        );
        $qb->from('Incwadi:Author', 'a');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->like('a.firstname', ':term'),
                $qb->expr()->like('a.surname', ':term'),
                $qb->expr()->like($name, ':term')
            )
        );

        $term = preg_replace('#[%\*]#', '', $term);
        $qb->setParameter('term', '%'.$term.'%');
        $qb->setMaxResults(self::LIMIT);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
