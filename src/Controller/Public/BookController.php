<?php

namespace App\Controller\Public;

use App\Entity\Book;
use App\Entity\Branch;
use App\Service\Cover\ShowCover;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


#[Route(path: '/api/public/book')]
class BookController extends AbstractController
{
    #[Route(path: '/find', methods: ['GET'])]
    public function find(Request $request): JsonResponse
    {
        return $this->json(
            $this
                ->getDoctrine()
                ->getRepository(Book::class)
                ->findDemanded(
                    json_decode(
                        $request->query->get('options'),
                        true
                    ),
                    true
                )
        );
    }

    /**
     * @Security("!book.getSold() and !book.getRemoved() and !book.getReserved()")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Book $book): JsonResponse
    {
        return $this->json([
            'id' => $book->getId(),
            'currency' => $book->getBranch()->getCurrency(),
            'title' => $book->getTitle(),
            'shortDescription' => $book->getShortDescription(),
            'authorFirstname' => $book->getAuthor()->getFirstname(),
            'authorSurname' => $book->getAuthor()->getSurname(),
            'genre' => $book->getGenre() ? $book->getGenre()->getName() : null,
            'price' => $book->getPrice(),
            'releaseYear' => $book->getReleaseYear(),
            'branchName' => $book->getBranch()->getName(),
            'branchOrdering' => $book->getBranch()->getOrdering(),
            'branchCart' => $book->getBranch()->getCart(),
            'cond' => $book->getCond() ? $book->getCond()->getName() : null,
            'format_name' => $book->getFormat() ? $book->getFormat()->getName() : null,
        ]);
    }

    #[Route(path: '/recommendation/{branch}', methods: ['GET'])]
    public function recommendation(Branch $branch, ShowCover $cover): JsonResponse
    {
        if (!$branch->getPublic()) {
            return $this->json(['books' => [], 'counter' => 0]);
        }

        $books = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->findBy([
                'branch' => $branch,
                'sold' => false,
                'removed' => false,
                'reserved' => false,
                'recommendation' => true,
            ]);

        $processed = [];
        foreach ($books as $book) {
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
                    'format_name' => $book->getFormat() ? $book->getFormat()->getName() : null,
                    'branchName' => $book->getBranch()->getName(),
                    'branchOrdering' => $book->getBranch()->getOrdering(),
                    'cond' => null !== $book->getCond() ? $book->getCond()->getName() : null,
                ],
                $cover->show($book)
            );
        }

        return $this->json([
            'books' => $processed,
            'counter' => count($processed),
        ]);
    }

    #[Route(path: '/cover/{book}_{dimensions}.jpg', methods: ['GET'])]
    public function image(Book $book, string $dimensions, ShowCover $cover): BinaryFileResponse
    {
        $width = (int) explode('x', $dimensions)[0];

        $size = 's';
        if ($width >= 200) {
            $size = 'm';
        }
        if ($width >= 400) {
            $size = 'l';
        }

        $file = $cover->getCoverPath($size, $book->getId());
        $filename = $book->getId().'-'.$size.'.jpg';

        return $this->file(
            $file,
            $filename,
            ResponseHeaderBag::DISPOSITION_INLINE
        );
    }
}
