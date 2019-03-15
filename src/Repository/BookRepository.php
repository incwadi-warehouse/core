<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Repository;

use Baldeweg\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    const LIMIT = 20;

    const OFFSET = 0;


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findDemanded(array $criteria, ?int $limit = self::LIMIT, ?int $offset = self::OFFSET)
    {
        $criteria['term'] = preg_replace('/[%\*]/', '', $criteria['term']);
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('b');
        $qb->from('Baldeweg:Book', 'b');
        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('b.stocked', ':stocked'),
                $qb->expr()->orX(
                    $qb->expr()->like('b.title', ':term'),
                    $qb->expr()->like('b.author', ':term')
                ),
                $this->branch($qb, $criteria['branch']),
                ($criteria['date'] !== null ? $qb->expr()->lte('b.added', ':date') : null),
                $this->genre($qb, $criteria['genre'])
            )
        );
        $qb->setParameter('term', '%' . $criteria['term'] . '%');
        $qb->setParameter('stocked', $criteria['stocked']);
        if (is_array($criteria['branch']) && $criteria['branch'][0] !== 'null' && $criteria['branch'][0] !== 'all') {
            $qb->setParameter('branch', $criteria['branch']);
        }
        if ($criteria['date']) {
            $qb->setParameter('date', $criteria['date']);
        }
        if (is_array($criteria['genre']) && $criteria['genre'][0] !== 'null' && $criteria['genre'][0] !== 'all') {
            $qb->setParameter('genre', $criteria['genre']);
        }
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    private function branch($qb, $branch) {
        if ($branch[0] === 'null') {
            return $qb->expr()->isNull('b.branch');
        }
        if ($branch[0] === 'all') {
            return;
        }
        if (is_array($branch)) {
            return $qb->expr()->in('b.branch', ':branch');
        }

        return;
    }

    private function genre($qb, $genre) {
        if ($genre[0] === 'null') {
            return $qb->expr()->isNull('b.genre');
        }
        if ($genre[0] === 'all') {
            return;
        }
        if (is_array($genre)) {
            return $qb->expr()->in('b.genre', ':genre');
        }

        return;
    }
}
