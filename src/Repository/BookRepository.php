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

    public function findDemanded(string $term, ?int $limit = self::LIMIT, ?int $offset = self::OFFSET)
    {
        $term = preg_replace('/[%\*]/', '', $term);
        $query = $this->getEntityManager()->createQuery('
            SELECT b
            FROM Baldeweg:Book b
            WHERE b.title LIKE :term
            OR b.author LIKE :term
        ');
        $query->setParameter('term', '%' . $term . '%');
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query->getResult();
    }
}
