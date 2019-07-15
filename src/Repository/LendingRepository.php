<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Incwadi\Core\Entity\Lending;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Lending|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lending|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lending[]    findAll()
 * @method Lending[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LendingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Lending::class);
    }
}
