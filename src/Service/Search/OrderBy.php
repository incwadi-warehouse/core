<?php

namespace App\Service\Search;

use Doctrine\ORM\QueryBuilder;

class OrderBy
{
    private Validator $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
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

        $field = 'b.'.$orderBy['field'];
        if ('genre' === $orderBy['field']) {
            $field = 'g.name';
        }
        if ('author' === $orderBy['field']) {
            $field = 'a.surname';
        }

        return $qb->orderBy(
            $field,
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
