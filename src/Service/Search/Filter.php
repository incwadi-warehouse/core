<?php

namespace App\Service\Search;

use Doctrine\ORM\QueryBuilder;

class Filter
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
        if (!$this->validator->isValidOperator($filter['operator'])) {
            return false;
        }

        return true;
    }

    private function createQuery(QueryBuilder $qb, array $filter, string $fieldId): mixed
    {
        switch ($filter['operator']) {
            case 'eq':
                return $qb->expr()->eq('b.'.$filter['field'], ':'.$fieldId);
            break;
            case 'gte':
                return $qb->expr()->gte('b.'.$filter['field'], ':'.$fieldId);
            break;
            case 'gt':
                return $qb->expr()->gt('b.'.$filter['field'], ':'.$fieldId);
            break;
            case 'lte':
                return $qb->expr()->lte('b.'.$filter['field'], ':'.$fieldId);
            break;
            case 'lt':
                return $qb->expr()->lt('b.'.$filter['field'], ':'.$fieldId);
            break;
            case 'in':
                return $qb->expr()->in('b.'.$filter['field'], ':'.$fieldId);
            break;
            case 'null':
                return $qb->expr()->isNull('b.'.$filter['field']);
            case 'notNull':
                return $qb->expr()->isNotNull('b.'.$filter['field']);
            default:
                return null;
            break;
        }
    }

    private function setParam(QueryBuilder $qb, array $filter, mixed $query, string $fieldId): void
    {
        if ('null' === $filter['operator'] || 'notNull' === $filter['operator'] || null === $query) {
            return;
        }

        if ('added' === $filter['field']) {
            $qb->setParameter(
                $fieldId,
                new \DateTime('@'.$filter['value']),
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
