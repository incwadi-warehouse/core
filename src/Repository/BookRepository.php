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

    public function findDemanded(array $criteria, string $orderBy='default', int $limit = self::LIMIT, int $offset = self::OFFSET)
    {
        $criteria['term'] = preg_replace('/[%\*]/', '', $criteria['term']);

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('b');
        $qb->from('Baldeweg:Book', 'b');

        $criteria['lending'] ? $qb->leftJoin('b.lending', 'l') : null;

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('b.stocked', ':stocked'),
                $this->term($qb, $criteria['term']),
                $this->branch($qb, $criteria['branch']),
                $this->added($qb, $criteria['date']),
                $this->genre($qb, $criteria['genre']),
                $this->lending($qb, $criteria['lending'])
            )
        );

        $qb->orderBy($this->orderings()[$orderBy][0], $this->orderings()[$orderBy][1]);

        $criteria['term'] ? $qb->setParameter('term', '%' . $criteria['term'] . '%') : null;
        $qb->setParameter('stocked', array_key_exists($criteria['stocked']) ? $criteria['stocked'] : true);
        if ($criteria['branch'] !== 'none' && $criteria['branch'] !== 'any') {
            $qb->setParameter('branch', explode(',', trim($criteria['branch'])));
        }
        if ($criteria['date']) {
            $qb->setParameter('date', new \DateTime('@' . $criteria['date']));
        }
        if ($criteria['genre'] !== 'none' && $criteria['genre'] !== 'any') {
            $qb->setParameter('genre', explode(',', trim($criteria['genre'])));
        }
        if ($criteria['lending']) {
            $qb->setParameter('lending', new \DateTime('@', $criteria['lending']));
        }
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    private function orderings() {
        return [
            'default' => ['b.id', 'ASC'],
            'genre_asc' => ['b.genre', 'ASC'],
            'genre_desc' => ['b.genre', 'DESC'],
            'added_asc' => ['b.added', 'ASC'],
            'added_desc' => ['b.added', 'DESC'],
            'title_asc' => ['b.title', 'ASC'],
            'title_desc' => ['b.title', 'DESC'],
            'author_asc' => ['b.author', 'ASC'],
            'author_desc' => ['b.author', 'DESC'],
            'price_asc' => ['b.price', 'ASC'],
            'price_desc' => ['b.price', 'DESC'],
            'yearOfPublication_asc' => ['b.yearOfPublication', 'ASC'],
            'yearOfPublication_desc' => ['b.yearOfPublication', 'DESC'],
            'type_asc' => ['b.type', 'ASC'],
            'type_desc' => ['b.type', 'DESC'],
            'premium_asc' => ['b.premium', 'ASC'],
            'premium_desc' => ['b.premium', 'DESC']
        ];
    }

    private function term($qb, $term) {
        if ($term) {
            return $qb->expr()->orX(
                $qb->expr()->like('b.title', ':term'),
                $qb->expr()->like('b.author', ':term')
            );
        }

        return;
    }

    private function branch($qb, $branch) {
        if ($branch === 'none') {
            return $qb->expr()->isNull('b.branch');
        }
        if ($branch === 'any') {
            return $qb->expr()->isNotNull('b.branch');
        }

        return $qb->expr()->in('b.branch', ':branch');
    }

    private function genre($qb, $genre) {
        if ($genre === 'none') {
            return $qb->expr()->isNull('b.genre');
        }
        if ($genre === 'any') {
            return $qb->expr()->isNotNull('b.genre');
        }

        return $qb->expr()->in('b.genre', ':genre');
    }

    private function lending($qb, $lending) {
        if ($lending) {
            return $qb->expr()->lte('l.lendOn', ':lending');
        }

        return;
    }

    private function added($qb, $date) {
        return $date !== null ? $qb->expr()->lte('b.added', ':date') : null;
    }
}
