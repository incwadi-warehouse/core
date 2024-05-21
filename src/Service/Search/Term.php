<?php

namespace App\Service\Search;

use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;

class Term
{
    public function term(QueryBuilder $qb, ?string $term = null): ?Orx
    {
        $term = trim($term);

        if (!$term) {
            return null;
        }
        $term = preg_replace('#[^a-zA-Z0-9 äöüß]#', '', $term);
        if (!$term) {
            return null;
        }

        $qb->setParameter('term', '%'.$term.'%');

        return $qb->expr()->orX(
            $qb->expr()->like('b.title', ':term'),
            $qb->expr()->like('a.firstname', ':term'),
            $qb->expr()->like('a.surname', ':term'),
            $qb->expr()->like(
                $this->author($qb, 'a.firstname', 'a.surname', ' '),
                ':term'
            ),
            $qb->expr()->like(
                $this->author($qb, 'a.surname', 'a.firstname', ' '),
                ':term'
            ),
            $qb->expr()->like(
                $this->author($qb, 'a.surname', 'a.firstname', ', '),
                ':term'
            ),
            $qb->expr()->like(
                $this->author($qb, 'a.surname', 'a.firstname', ','),
                ':term'
            ),
            $qb->expr()->like('t.name', ':term'),
            $qb->expr()->like('g.name', ':term')
        );
    }

    private function author(QueryBuilder $qb, string $first, string $last, string $separator): Func
    {
        return $qb->expr()->concat(
            $first,
            $qb->expr()->concat(
                $qb->expr()->literal($separator),
                $last
            )
        );
    }
}
