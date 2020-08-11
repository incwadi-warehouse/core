<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Branch;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    /**
     * @var int
     */
    const LIMIT = 20;

    /**
     * @var int
     */
    const CLEAR_LIMIT = 28;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function deleteBooks(int $clearLimit = self::CLEAR_LIMIT): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->delete('Incwadi:Book', 'b');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->lte('b.soldOn', ':date'),
                $qb->expr()->lte('b.removedOn', ':date')
            )
        );
        $date = new \DateTime();
        $date->sub(new \DateInterval('P'.$clearLimit.'D'));

        $qb->setParameter(
            'date',
            $date
        );

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function deleteBooksByBranch(Branch $branch): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->delete('Incwadi:Book', 'b');
        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->orX(
                    $qb->expr()->eq('b.sold', ':state'),
                    $qb->expr()->eq('b.removed', ':state')
                ),
                $qb->expr()->eq('b.branch', ':branch')
            )
        );

        $qb->setParameter('state', true);
        $qb->setParameter('branch', $branch);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findDemanded(
        array $criteria,
        string $orderBy = 'default',
        int $limit = self::LIMIT
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('b');
        $qb->from('Incwadi:Book', 'b');

        $qb->leftJoin('b.author', 'a');
        $qb->leftJoin('b.tags', 't');

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('b.sold', ':sold'),
                $qb->expr()->eq('b.removed', ':removed'),
                $this->term($qb, $criteria['term']),
                $this->branch($qb, $criteria['branch']),
                $this->added($qb, $criteria['added']),
                $this->genre($qb, $criteria['genre']),
                $this->lending($qb, $criteria['lending']),
                $this->releaseYear($qb, $criteria['releaseYear']),
                $this->type($qb, $criteria['type'])
            )
        );

        $qb->orderBy($this->orderings()[$orderBy]['field'], $this->orderings()[$orderBy]['direction']);

        $this->setParams($qb, $criteria);
        $qb->setMaxResults($limit);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    private function setParams(QueryBuilder $qb, array $criteria)
    {
        $criteria['term'] = preg_replace('#[%\*]#', '', $criteria['term']);
        if ($criteria['term']) {
            $qb->setParameter('term', '%'.$criteria['term'].'%');
        }

        $qb->setParameter(
            'sold',
            array_key_exists('sold', $criteria) ? $criteria['sold'] : false
        );
        $qb->setParameter(
            'removed',
            array_key_exists('removed', $criteria) ? $criteria['removed'] : false
        );

        if ($criteria['branch']) {
            $qb->setParameter(
                'branch',
                explode(',', trim($criteria['branch']))
            );
        }

        if ($criteria['genre']) {
            $qb->setParameter(
                'genre',
                explode(',', trim($criteria['genre']))
            );
        }

        if ($criteria['lending']) {
            $qb->setParameter(
                'lending',
                new \DateTime('@'.$criteria['lending'])
            );
        }

        if ($criteria['added']) {
            $qb->setParameter(
                'added',
                new \DateTime('@'.$criteria['added'])
            );
        }

        if ($criteria['releaseYear']) {
            $qb->setParameter(
                'releaseYear',
                (int) $criteria['releaseYear']
            );
        }

        if ($criteria['type']) {
            if (!in_array($criteria['type'], Book::TYPES)) {
                $qb->setParameter(
                    'type',
                    null
                );
            }

            $qb->setParameter(
                'type',
                $criteria['type']
            );
        }
    }

    private function term(QueryBuilder $qb, ?string $term)
    {
        if ($term) {
            $name = $qb->expr()->concat(
                'a.firstname',
                $qb->expr()->concat($qb->expr()->literal(' '),
                'a.surname')
            );
            $nameReverse = $qb->expr()->concat(
                'a.surname',
                $qb->expr()->concat($qb->expr()->literal(' '),
                'a.firstname')
            );
            $nameCommaAndSpace = $qb->expr()->concat(
                'a.surname',
                $qb->expr()->concat($qb->expr()->literal(', '),
                'a.firstname')
            );
            $name4Comma = $qb->expr()->concat(
                'a.surname',
                $qb->expr()->concat($qb->expr()->literal(','),
                'a.firstname')
            );

            return $qb->expr()->orX(
                $qb->expr()->like('b.title', ':term'),
                $qb->expr()->like('a.firstname', ':term'),
                $qb->expr()->like('a.surname', ':term'),
                $qb->expr()->like($name, ':term'),
                $qb->expr()->like($nameReverse, ':term'),
                $qb->expr()->like($nameCommaAndSpace, ':term'),
                $qb->expr()->like($name4Comma, ':term'),
                $qb->expr()->like('t.name', ':term')
            );
        }

        return null;
    }

    private function branch(QueryBuilder $qb, ?string $branch)
    {
        if (!$branch) {
            return $qb->expr()->isNotNull('b.branch');
        }

        return $qb->expr()->in('b.branch', ':branch');
    }

    private function genre(QueryBuilder $qb, ?string $genre)
    {
        $qb->leftJoin('b.genre', 'g');
        if (!$genre) {
            return $qb->expr()->isNotNull('b.genre');
        }

        return $qb->expr()->in('b.genre', ':genre');
    }

    private function lending(QueryBuilder $qb, ?int $lending)
    {
        if ($lending) {
            return $qb->expr()->lte('b.lendOn', ':lending');
        }

        return null;
    }

    private function added(QueryBuilder $qb, ?int $added)
    {
        if ($added) {
            return $qb->expr()->lte('b.added', ':added');
        }

        return null;
    }

    private function releaseYear(QueryBuilder $qb, ?int $releaseYear)
    {
        if ($releaseYear) {
            return $qb->expr()->eq('b.releaseYear', ':releaseYear');
        }

        return null;
    }

    private function type(QueryBuilder $qb, ?string $type)
    {
        if ($type) {
            return $qb->expr()->eq('b.type', ':type');
        }

        return null;
    }

    private function orderings(): array
    {
        return [
            'asc' => [
                'field' => 'b.id',
                'direction' => 'ASC'
            ],
            'desc' => [
                'field' => 'b.id',
                'direction' => 'DESC'
            ],
            'genre_asc' => [
                'field' => 'g.name',
                'direction' => 'ASC'
            ],
            'genre_desc' => [
                'field' => 'g.name',
                'direction' => 'DESC'
            ],
            'added_asc' => [
                'field' => 'b.added',
                'direction' => 'ASC'
            ],
            'added_desc' => [
                'field' => 'b.added',
                'direction' => 'DESC'
            ],
            'title_asc' => [
                'field' => 'b.title',
                'direction' => 'ASC'
            ],
            'title_desc' => [
                'field' => 'b.title',
                'direction' => 'DESC'
            ],
            'author_asc' => [
                'field' => 'a.surname',
                'direction' => 'ASC'
            ],
            'author_desc' => [
                'field' => 'a.surname',
                'direction' => 'DESC'
            ],
            'price_asc' => [
                'field' => 'b.price',
                'direction' => 'ASC'
            ],
            'price_desc' => [
                'field' => 'b.price',
                'direction' => 'DESC'
            ],
            'releaseYear_asc' => [
                'field' => 'b.releaseYear',
                'direction' => 'ASC'
            ],
            'releaseYear_desc' => [
                'field' => 'b.releaseYear',
                'direction' => 'DESC'
            ],
            'type_asc' => [
                'field' => 'b.type',
                'direction' => 'ASC'
            ],
            'type_desc' => [
                'field' => 'b.type',
                'direction' => 'DESC'
            ]
        ];
    }
}
