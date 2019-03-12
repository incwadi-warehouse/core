<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Repository;

use Baldeweg\Entity\Lend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Lend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lend[]    findAll()
 * @method Lend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LendRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Lend::class);
    }
}
