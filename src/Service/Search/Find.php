<?php

namespace App\Service\Search;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Andx;
use App\Entity\Book;

class Find implements FindInterface
{
    /**
     * @var int
     */
    public const LIMIT = 20;
    private EntityManagerInterface $em;
    private Term $term;
    private Filter $filter;
    private OrderBy $orderBy;
    private ?array $forcedFilters = null;

    public function __construct(EntityManagerInterface $em, Term $term, Filter $filter, OrderBy $orderBy)
    {
        $this->em = $em;
        $this->term = $term;
        $this->filter = $filter;
        $this->orderBy = $orderBy;
    }

    public function setFields(array $fields): void
    {
        $this->filter->setFields($fields);
        $this->orderBy->setFields($fields);
    }

    public function setForcedFilters(array $forcedFilters): void
    {
        $this->forcedFilters = $forcedFilters;
    }

    public function find(array $options): array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('b');
        $qb->from(Book::class, 'b');
        $qb->leftJoin('b.author', 'a');
        $qb->leftJoin('b.tags', 't');
        $qb->leftJoin('b.genre', 'g');

        if (isset($options['term']) || isset($options['filter'])) {
            $qb->where(
                $this->parseOptions($qb, $options)
            );
        }
        if (isset($options['orderBy']['book'][0])) {
            $this->orderBy->orderBy($qb, $options['orderBy']['book'][0]);
        }
        $qb->setMaxResults(
            isset($options['limit']) ? $options['limit'] : self::LIMIT
        );
        if (isset($options['offset'])) {
            $qb->setFirstResult($options['offset']);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function count(array $options): int
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('COUNT(b)');
        $qb->from(Book::class, 'b');
        $qb->leftJoin('b.author', 'a');
        $qb->leftJoin('b.tags', 't');
        $qb->leftJoin('b.genre', 'g');

        if (isset($options['term']) || isset($options['filter'])) {
            $qb->where(
                $this->parseOptions($qb, $options)
            );
        }

        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }

    private function parseOptions($qb, ?array $options): ?Andx
    {
        $query = $qb->expr()->andX();

        if (isset($options['term'])) {
            $query->add(
                $this->term->term($qb, $options['term'])
            );
        }

        if (isset($options['filter'])) {
            foreach ($options['filter'] as $filter) {
                $query->add(
                    $this->filter->filter($qb, $filter)
                );
            }
        }
        if (null !== $this->forcedFilters) {
            foreach ($this->forcedFilters as $forcedFilter) {
                $query->add(
                    $this->filter->filter($qb, $forcedFilter, true)
                );
            }
        }

        return $query;
    }
}
