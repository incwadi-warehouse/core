<?php

namespace App\Service\Search;

use Doctrine\ORM\QueryBuilder;

class Filter
{
    public function __construct(private readonly Validator $validator)
    {
    }

    public function setFields(array $fields): void
    {
        $this->validator->setFields($fields);
    }

    public function filter(QueryBuilder $qb, array $filter, bool $forced = false): mixed
    {
        if (!$this->canFilter($filter, $forced)) {
            return null;
        }

        $fieldId = $this->getFieldId($filter['field']);
        $query = $this->createQuery($qb, $filter, $fieldId);
        $this->setParam($qb, $filter, $query, $fieldId);

        return $query;
    }

    private function canFilter(array $filter, bool $forced): bool
    {
        if (!$forced && !$this->validator->isValidField($filter['field'])) {
            return false;
        }

        if (!isset($filter['value'])) {
            return false;
        }


        return $this->validator->isValidOperator($filter['operator']);
    }

    private function createQuery(QueryBuilder $qb, array $filter, string $fieldId): mixed
    {
        return match ($filter['operator']) {
            'eq' => $qb->expr()->eq('b.'.$filter['field'], ':'.$fieldId),
            'gte' => $qb->expr()->gte('b.'.$filter['field'], ':'.$fieldId),
            'gt' => $qb->expr()->gt('b.'.$filter['field'], ':'.$fieldId),
            'lte' => $qb->expr()->lte('b.'.$filter['field'], ':'.$fieldId),
            'lt' => $qb->expr()->lt('b.'.$filter['field'], ':'.$fieldId),
            'in' => $qb->expr()->in('b.'.$filter['field'], ':'.$fieldId),
            'null' => $qb->expr()->isNull('b.'.$filter['field']),
            'notNull' => $qb->expr()->isNotNull('b.'.$filter['field']),
            default => null,
        };
    }

    private function setParam(QueryBuilder $qb, array $filter, mixed $query, string $fieldId): void
    {
        if ('null' === $filter['operator'] || 'notNull' === $filter['operator'] || null === $query) {
            return;
        }

        if ('added' === $filter['field']) {
            $qb->setParameter(
                $fieldId,
                new \DateTime($filter['value']),
                'datetime'
            );
        } else {
            $qb->setParameter($fieldId, $filter['value']);
        }
    }

    private function getFieldId(string $name): string
    {
        return str_replace(
            '.',
            '_',
            \uniqid($name.'_', true)
        );
    }
}
