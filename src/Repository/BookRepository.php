<?php

namespace Incwadi\Core\Repository;

use Baldeweg\Bundle\BookBundle\Search\Find;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Service\Cover\CoverRemove;

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
    public const KEEP_REMOVED_DAYS = 28;

    private CoverRemove $cover;

    private Find $find;

    public function __construct(ManagerRegistry $registry, CoverRemove $cover, Find $find)
    {
        parent::__construct($registry, Book::class);
        $this->cover = $cover;
        $this->find = $find;
    }

    public function findDemanded(array $options, bool $isPublic = false): array
    {
        $fields = [
            'branch',
            'added',
            'title',
            'author',
            'genre',
            'price',
            'sold',
            'soldOn',
            'removed',
            'removedOn',
            'reserved',
            'reservedAt',
            'releaseYear',
            'type',
            'lendTo',
            'lendOn',
            'recommendation',
        ];
        if ($isPublic) {
            if (strlen($options['term']) < 1) {
                throw new \Exception('There is no term!');
            }

            $branch = false;
            foreach ($options['filter'] as $filter) {
                if ('branch' === $filter['field']) {
                    $branch = $filter['value'];
                }
            }
            if ($branch) {
                $branchObj = $this->getEntityManager()->getRepository(Branch::class)->find($branch);
                if (!$branchObj->getPublic()) {
                    throw new \Exception('No valid branch chosen!');
                }
            }

            $fields = ['branch'];
            $this->find->setForcedFilters([
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
            ]);
        }

        $this->find->setFields($fields);

        $result = $this->find->find($options);
        $counter = $this->find->count($options);

        if ($isPublic) {
            return [
                'books' => $this->getBook($result),
                'counter' => $counter,
            ];
        }

        return [
            'books' => $result,
            'counter' => $counter,
        ];
    }

    private function getBook(array $books): array
    {
        $processed = [];
        foreach ($books as $book) {
            $processed[] = [
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
            ];
        }

        return $processed;
    }

    public function deleteBooks(int $clearLimit = self::KEEP_REMOVED_DAYS): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        // $qb->delete('Incwadi:Book', 'b');
        $qb->select('b');
        $qb->from('Incwadi:Book', 'b');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->lte('b.soldOn', ':date'),
                $qb->expr()->lte('b.removedOn', ':date')
            )
        );
        $date = new \DateTime();
        $date->sub(new \DateInterval('P'.$clearLimit.'D'));

        $qb->setParameter('date', $date);

        $query = $qb->getQuery();
        $books = $query->getResult();

        foreach ($books as $item) {
            $this->deleteBook($item);
        }

        $this->getEntityManager()->flush();
    }

    public function deleteBooksByBranch(Branch $branch): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        // $qb->delete('Incwadi:Book', 'b');
        $qb->select('b');
        $qb->from('Incwadi:Book', 'b');
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
        $books = $query->getResult();

        foreach ($books as $item) {
            $this->deleteBook($item);
        }

        $this->getEntityManager()->flush();
    }

    public function findDuplicate(Book $book)
    {
        return $this->findOneBy(
            [
                'branch' => $book->getBranch(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'genre' => $book->getGenre(),
                'price' => $book->getPrice(),
                'sold' => $book->getSold(),
                'removed' => $book->getRemoved(),
                'releaseYear' => $book->getReleaseYear(),
                'type' => $book->getType(),
            ]
        );
    }

    private function deleteBook(Book $book): void
    {
        $this->cover->remove($book);
        $this->getEntityManager()->remove($book);
    }

    public function removeNotFoundBooks(Branch $branch): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->update(Book::class, 'b');
        $qb->set('b.removed', ':removed');
        $qb->set('b.removedOn', ':removedOn');
        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('b.inventory', ':inventory'),
                $qb->expr()->eq('b.branch', ':branch')
            )
        );

        $qb->setParameter('removed', true);
        $qb->setParameter('removedOn', new \DateTime());
        $qb->setParameter('inventory', false);
        $qb->setParameter('branch', $branch);

        $query = $qb->getQuery();
        $query->execute();
    }

    public function resetInventory(Branch $branch): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->update(Book::class, 'b');
        $qb->set('b.inventory', ':inventory');
        $qb->where(
            $qb->expr()->eq('b.branch', ':branch')
        );

        $qb->setParameter('inventory', null);
        $qb->setParameter('branch', $branch);

        $query = $qb->getQuery();
        $query->execute();
    }
}
