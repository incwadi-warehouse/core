<?php

namespace App\Repository;

use App\Entity\Condition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
