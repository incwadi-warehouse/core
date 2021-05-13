<?php

namespace Incwadi\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Service\Cover\CoverRemove;
use Incwadi\Core\Service\Search\Search;

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

    private $cover;

    public function __construct(ManagerRegistry $registry, Search $search, CoverRemove $cover)
    {
        parent::__construct($registry, Book::class);
        $this->search = $search;
        $this->cover = $cover;
    }

    public function findDemanded(array $options, bool $isPublic = false): array
    {
        $this->search->setPublic($isPublic);

        return $this->search->find($options);
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
}
