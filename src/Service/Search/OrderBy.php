<?php

namespace App\Service\Search;

use Doctrine\ORM\QueryBuilder;

class OrderBy
{
    public function __construct(private readonly Validator $validator)
    {
    }

    public function setFields(array $fields): void
    {
        $this->validator->setFields($fields);
    }

    public function orderBy(QueryBuilder $qb, array $orderBy): ?QueryBuilder
    {
        if (!isset($orderBy['field']) || !$this->validator->isValidField($orderBy['field'])) {
            return null;
        }

        if ($orderBy['field'] === 'genre') {
            return $qb->orderBy(
                'g.name',
                $this->getDirection($orderBy)
            );
        }

        if ($orderBy['field'] === 'author') {
            return $qb->orderBy(
                'a.surname',
                $this->getDirection($orderBy)
            )->addOrderBy(
                'a.firstname',
                $this->getDirection($orderBy));
        }

        return $qb->orderBy(
            'b.' . $orderBy['field'],
            $this->getDirection($orderBy)
        );
    }

    public function getDirection(array $orderBy): string
    {
        $direction = 'asc';
        if (isset($orderBy['direction']) && 'desc' === $orderBy['direction']) {
            $direction = 'desc';
        }

        return $direction;
    }
}
