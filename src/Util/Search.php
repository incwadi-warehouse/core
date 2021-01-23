<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Util;

use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Incwadi\Core\Entity\Book;

class Search
{
    const LIMIT = 20;

    private QueryBuilder $qb;

    // @fix make it easier to set this value
    private bool $isPublic = false;

    public function __construct(QueryBuilder $qb, bool $isPublic = false)
    {
        $this->qb = $qb;
        $this->isPublic = $isPublic;
    }

    public function find(array $options): array
    {
        $this->qb->select('b');
        $this->qb->from('Incwadi:Book', 'b');
        $this->qb->leftJoin('b.author', 'a');
        if (isset($options['term'])) {
            $this->qb->leftJoin('b.tags', 't');
        }
        $this->qb->leftJoin('b.genre', 'g');

        if ($this->isPublic) {
            $options['filter'] = [
                [
                    'field' => 'sold',
                    'operator' => 'eq',
                    'value' => '0',
                ],
                [
                    'field' => 'removed',
                    'operator' => 'eq',
                    'value' => '0',
                ],
            ];
        }
        if (isset($options['term']) || isset($options['filter'])) {
            $this->qb->where(
                $this->parseOptions($options)
            );
        }
        if (isset($options['orderBy']['book'][0])) {
            $this->setOrderBy($options['orderBy']['book'][0]);
        }
        $this->qb->setMaxResults(
            isset($options['limit']) ? $options['limit'] : self::LIMIT
        );

        $query = $this->qb->getQuery();
        $books = $query->getResult();

        $this->qb->setMaxResults(null);
        $query = $this->qb->getQuery();
        $counter = count($query->getResult());

        return [
            'books' => $books,
            'counter' => $counter,
        ];
    }

    private function parseOptions(?array $options): ?Andx
    {
        $query = $this->qb->expr()->andX();
        if (isset($options['term'])) {
            $query->add($this->term($options['term']));
        }
        if (isset($options['filter'])) {
            foreach ($options['filter'] as $filter) {
                $query->add(
                    $this->createQuery($filter)
                );
            }
        }

        return $query;
    }

    private function createQuery(array $filter)
    {
        if (!$this->isFieldNameValid($filter['field']) || !isset($filter['value'])) {
            return null;
        }

        $fieldName = $filter['field'];
        $fieldId = $this->getFieldId($fieldName);
        $fieldValue = $this->transformValue($filter['value'], $fieldName);

        $operator = $this->getOperator($filter['operator'], $fieldName);
        switch ($operator) {
            case 'eq':
                $query = $this->qb->expr()->eq('b.'.$fieldName, ':'.$fieldId);
            break;
            case 'gte':
                $query = $this->qb->expr()->gte('b.'.$fieldName, ':'.$fieldId);
            break;
            case 'gt':
                $query = $this->qb->expr()->gt('b.'.$fieldName, ':'.$fieldId);
            break;
            case 'lte':
                $query = $this->qb->expr()->lte('b.'.$fieldName, ':'.$fieldId);
            break;
            case 'lt':
                $query = $this->qb->expr()->lt('b.'.$fieldName, ':'.$fieldId);
            break;
            case 'in':
                $query = $this->qb->expr()->in('b.'.$fieldName, ':'.$fieldId);
            break;
            default:
                $query = null;
            break;
        }

        if (!$query) {
            return null;
        }

        $this->qb->setParameter($fieldId, $fieldValue);

        return $query;
    }

    private function term(?string $term): ?Orx
    {
        $term = preg_replace('#[%\*]#', '', $term);
        if (!$term) {
            // @fix: fail gracefully, dont return something if term does not contain at least one letter or number
            if ($this->isPublic) {
                throw new \Exception('There is no term!');
            }

            return null;
        }

        $this->qb->setParameter('term', '%'.$term.'%');

        $name = $this->qb->expr()->concat(
            'a.firstname',
            $this->qb->expr()->concat($this->qb->expr()->literal(' '),
            'a.surname')
        );
        $nameReverse = $this->qb->expr()->concat(
            'a.surname',
            $this->qb->expr()->concat($this->qb->expr()->literal(' '),
            'a.firstname')
        );
        $nameWithCommaAndSpace = $this->qb->expr()->concat(
            'a.surname',
            $this->qb->expr()->concat($this->qb->expr()->literal(', '),
            'a.firstname')
        );
        $nameWithComma = $this->qb->expr()->concat(
            'a.surname',
            $this->qb->expr()->concat($this->qb->expr()->literal(','),
            'a.firstname')
        );

        return $this->qb->expr()->orX(
            $this->qb->expr()->like('b.title', ':term'),
            $this->qb->expr()->like('a.firstname', ':term'),
            $this->qb->expr()->like('a.surname', ':term'),
            $this->qb->expr()->like($name, ':term'),
            $this->qb->expr()->like($nameReverse, ':term'),
            $this->qb->expr()->like($nameWithCommaAndSpace, ':term'),
            $this->qb->expr()->like($nameWithComma, ':term'),
            $this->qb->expr()->like('t.name', ':term')
        );
    }

    private function setOrderBy(array $orderBy): void
    {
        if (!isset($orderBy['field'])) {
            return;
        }
        if (!$this->isFieldNameValid($orderBy['field'])) {
            return;
        }

        $field = 'b.'.$orderBy['field'];
        if ('genre' === $orderBy['field']) {
            $field = 'g.name';
        }
        if ('author' === $orderBy['field']) {
            $field = 'a.surname';
        }

        $this->qb->orderBy(
            $field,
            isset($orderBy['direction']) ? $orderBy['direction'] : 'asc'
        );
    }

    private function getFieldId(string $fieldName): string
    {
        return str_replace(
            '.',
            '_',
            \uniqid($fieldName.'_', true)
        );
    }

    private function isFieldNameValid(string $field): bool
    {
        $ref = new \ReflectionClass(Book::class);
        foreach ($ref->getProperties() as $prop) {
            if ($field === $prop->name) {
                return true;
            }
        }

        return false;
    }

    private function transformValue($value, string $filter)
    {
        $ref = new \ReflectionClass(Book::class);
        foreach ($ref->getProperties() as $prop) {
            if ($filter === $prop->name) {
                $type = $prop->getType()->getName();
                if ('string' === $type) {
                    return (string) $value;
                }
                if ('int' === $type) {
                    return (int) $value;
                }
                if ('float' === $type) {
                    return (float) $value;
                }
                if ('bool' === $type) {
                    return (bool) $value;
                }
                if ('DateTime' === $type) {
                    return new \DateTime('@'.$value);
                }
            }
        }

        return $value;
    }

    private function getOperator(string $operator, string $fieldName): string
    {
        if (!in_array($operator, ['in', 'eq', 'gte', 'gt', 'lte', 'lt'])) {
            $operator = 'eq';
        }
        if (in_array($fieldName, ['sold', 'removed', 'type'])) {
            $operator = 'eq';
        }
        if ('in' === $operator && !in_array($fieldName, ['genre', 'branch'])) {
            $operator = 'eq';
        }

        return $operator;
    }
}
