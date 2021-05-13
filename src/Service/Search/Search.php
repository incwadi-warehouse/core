<?php

namespace Incwadi\Core\Service\Search;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Service\Cover\CoverShow;

class Search
{
    /**
     * @var int
     */
    public const LIMIT = 20;

    private QueryBuilder $qb;

    private EntityManagerInterface $em;

    private bool $isPublic = false;

    public function __construct(EntityManagerInterface $em)
    {
        $this->qb = $em->createQueryBuilder();
        $this->em = $em;
    }

    public function setPublic(bool $isPublic): void
    {
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
            $options['filter'] = [];
            $options['orderBy'] = [];

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
                [
                    'field' => 'reserved',
                    'operator' => 'eq',
                    'value' => '0',
                ],
                [
                    'field' => 'lendOn',
                    'operator' => 'null',
                    'value' => '0',
                ],
            ];

            if (strlen($options['term']) < 1) {
                throw new \Exception('There is no term!');
            }
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
        if (isset($options['offset'])) {
            $this->qb->setFirstResult($options['offset']);
        }

        $query = $this->qb->getQuery();
        $books = $query->getResult();

        $this->qb->setMaxResults(null);
        $this->qb->setFirstResult(null);

        $query = $this->qb->getQuery();
        $counter = count($query->getResult());

        if ($this->isPublic) {
            return [
                'books' => $this->getBook($books),
                'counter' => $counter,
            ];
        }

        return [
            'books' => $books,
            'counter' => $counter,
        ];
    }

    private function getBook(array $books): array
    {
        $processed = [];
        foreach ($books as $book) {
            $cover = new CoverShow();
            $processed[] = array_merge(
                [
                    'id' => $book->getId(),
                    'currency' => $book->getBranch()->getCurrency(),
                    'title' => $book->getTitle(),
                    'shortDescription' => $book->getShortDescription(),
                    'authorFirstname' => $book->getAuthor()->getFirstname(),
                    'authorSurname' => $book->getAuthor()->getSurname(),
                    'genre' => $book->getGenre()->getName(),
                    'price' => $book->getPrice(),
                    'releaseYear' => $book->getReleaseYear(),
                    'type' => $book->getType(),
                    'branchName' => $book->getBranch()->getName(),
                    'branchOrdering' => $book->getBranch()->getOrdering(),
                    'cond' => $book->getCond() ? $book->getCond()->getName() : null,
                ],
                $cover->show($book)
            );
        }

        return $processed;
    }

    private function parseOptions(?array $options): ?Andx
    {
        if ($this->isPublic) {
            $branch = false;
            foreach ($options['filter'] as $filter) {
                if ('branch' === $filter['field']) {
                    $branch = $filter['value'];
                }
            }
            $options['filter'] = [];
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
                [
                    'field' => 'reserved',
                    'operator' => 'eq',
                    'value' => '0',
                ],
            ];
            if ($branch) {
                $branchObj = $this->em->getRepository(Branch::class)->find($branch);
                if (!$branchObj->getPublic()) {
                    throw new \Exception('No valid branch chosen!');
                }
                $options['filter'][] = [
                        'field' => 'branch',
                        'operator' => 'eq',
                        'value' => $branch,
                    ];
            }
        }

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
            case 'null':
                return $this->qb->expr()->isNull('b.'.$fieldName);
            default:
                $query = null;
            break;
        }

        if (null === $query) {
            return null;
        }

        $this->qb->setParameter($fieldId, $fieldValue);

        return $query;
    }

    private function term(?string $term): ?Orx
    {
        $term = preg_replace('#[%\*]#', '', $term);
        if (!$term) {
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
            $this->qb->expr()->like('t.name', ':term'),
            $this->qb->expr()->like('g.name', ':term')
        );
    }

    private function setOrderBy(array $orderBy): void
    {
        if ($this->isPublic) {
            $options['orderBy'] = [];
        }
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
                if (\DateTime::class === $type) {
                    return new \DateTime('@'.$value);
                }
            }
        }

        return $value;
    }

    private function getOperator(string $operator, string $fieldName): string
    {
        if (!in_array($operator, ['in', 'eq', 'gte', 'gt', 'lte', 'lt', 'null'])) {
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
