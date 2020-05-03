<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Incwadi\Core\Entity\Condition;

/**
 * @method Condition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Condition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Condition[]    findAll()
 * @method Condition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Condition::class);
    }
}
