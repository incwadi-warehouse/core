<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Inventory;
use App\Form\BookCoverType;
use App\Form\BookType;
use App\Service\Cover\CoverRemove;
use App\Service\Cover\CoverShow;
use App\Service\Cover\CoverUpload;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[\Symfony\Component\Routing\Annotation\Route(path: '/api/book')]
class BookController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/find', methods: ['GET'])]
    public function find(Request $request) : JsonResponse
    {
        return $this->json(
            $this
                ->getDoctrine()
                ->getRepository(Book::class)
                ->findDemanded(
                    json_decode(
                        $request->query->get('options'),
                        true
                    )
                )
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/clean', methods: ['DELETE'])]
    public function clean() : JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Book::class)->deleteBooksByBranch(
            $this->getUser()->getBranch()
        );
        $em->flush();

        return $this->json(['msg' => 'Cleaned up successfully!']);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/stats', methods: ['GET'])]
    public function stats() : JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Book::class);
        $branch = $this->getUser()->getBranch();

        $all = count($repo->findByBranch($branch));
        $available = count($repo->findBy([
            'branch' => $branch,
            'sold' => false,
            'removed' => false,
            'reserved' => false,
        ]));
        $reserved = count($repo->findBy([
            'branch' => $branch,
            'reserved' => true,
        ]));
        $sold = count($repo->findBy(
            [
                'branch' => $branch,
                'sold' => true,
            ]
        ));
        $removed = count($repo->findBy([
            'branch' => $branch,
            'removed' => true,
        ]));

        return $this->json([
            'all' => $all,
            'available' => $available,
            'reserved' => $reserved,
            'sold' => $sold,
            'removed' => $removed,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER') and book.getBranch() === user.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/inventory/found/{book}', methods: ['PUT'])]
    public function inventoryFound(Book $book) : JsonResponse
    {
        $inventory = $this->getDoctrine()->getRepository(Inventory::class)->findActive($this->getUser()->getBranch());
        $inventory->setFound($book->getInventory() ? $inventory->getFound() - 1 : $inventory->getFound() + 1);

        $book->setInventory($book->getInventory() ? null : true);

        $this->getDoctrine()->getManager()->flush();

        return $this->json($book);
    }

    /**
     * @Security("is_granted('ROLE_USER') and book.getBranch() === user.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/inventory/notfound/{book}', methods: ['PUT'])]
    public function inventoryNotFound(Book $book) : JsonResponse
    {
        $inventory = $this->getDoctrine()->getRepository(Inventory::class)->findActive($this->getUser()->getBranch());
        $inventory->setNotFound(false === $book->getInventory() ? $inventory->getNotFound() - 1 : $inventory->getNotFound() + 1);

        $book->setInventory(false === $book->getInventory() ? null : false);

        $this->getDoctrine()->getManager()->flush();

        return $this->json($book);
    }

    /**
     * @Security("is_granted('ROLE_USER') and book.getBranch() === user.getBranch() or is_granted('ROLE_ADMIN')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['GET'])]
    public function show(Request $request, Book $book) : JsonResponse
    {
        return $this->json($book);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/new', methods: ['POST'])]
    public function new(Request $request) : JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $book = new Book();
        $book->setBranch(
            $this->getUser()->getBranch()
        );
        $form = $this->createForm(BookType::class, $book);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        $existingBook = $em->getRepository(Book::class)->findDuplicate($book);
        if (null !== $existingBook) {
            return $this->json([
                'msg' => 'Book not saved, because it exists already!',
            ], 409);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($book);
            $em->flush();

            return $this->json($book);
        }

        return $this->json([
            'msg' => 'Please enter a valid book!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['PUT'])]
    public function edit(Request $request, Book $book) : JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(BookType::class, $book);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        $existingBook = $em->getRepository(Book::class)->findDuplicate($book);
        if (null !== $existingBook && $existingBook->getId() !== $book->getId()) {
            return $this->json([
            'msg' => 'Book not saved, because it exists already!',
            ], 409);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // sold
            if (true === $book->getSold() && null === $book->getSoldOn()) {
                $book->setSoldOn(new \DateTime());
            }
            // revert sold
            if (false === $book->getSold() && null !== $book->getSoldOn()) {
                $book->setSoldOn(null);
            }
            // removed
            if (true === $book->getRemoved() && null === $book->getRemovedOn()) {
                $book->setRemovedOn(new \DateTime());
            }
            // revert removed
            if (false === $book->getRemoved() && null !== $book->getRemovedOn()) {
                $book->setRemovedOn(null);
            }
            // reserved
            if (true === $book->getReserved() && null === $book->getReservedAt()) {
                $book->setReservedAt(new \DateTime());
            }
            // revert reserved
            if (false === $book->getReserved() && null !== $book->getReservedAt()) {
                $book->setReservedAt(null);
                $book->setReservation(null);
            }
            $em->flush();

            return $this->json($book);
        }

        return $this->json([
            'msg' => 'Please enter a valid book!',
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/cover/{id}', methods: ['GET'])]
    public function showCover(Request $request, Book $book, CoverShow $cover) : JsonResponse
    {
        return $this->json($cover->show($book));
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/cover/{id}', methods: ['POST'])]
    public function cover(Request $request, Book $book, CoverUpload $coverUpload) : JsonResponse
    {
        $form = $this->createForm(BookCoverType::class, $book);
        $form->submit(['cover' => $request->files->get('cover')]);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('cover')->getData();
            $coverUpload->upload($book, $file);

            return $this->json($book);
        }

        throw new \Error('Could not upload image ('.$form->get('cover')->getData()->getErrorMessage().').');
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/cover/{id}', methods: ['DELETE'])]
    public function deleteCover(Request $request, Book $book, CoverRemove $cover) : JsonResponse
    {
        $cover->remove($book);

        return $this->json(['msg' => 'Cover was deleted.']);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/sell/{id}', methods: ['PUT'])]
    public function sell(Book $book) : JsonResponse
    {
        $book->setSold(!$book->getSold());
        $book->setSoldOn(null === $book->getSoldOn() ? new \DateTime() : null);

        $book->setReserved(false);
        $book->setReservedAt(null);

        $this->getDoctrine()->getManager()->flush();

        return $this->json($book);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/remove/{id}', methods: ['PUT'])]
    public function remove(Book $book) : JsonResponse
    {
        $book->setRemoved(!$book->getRemoved());
        $book->setRemovedOn(
            null === $book->getRemovedOn() ? new \DateTime() : null
        );

        $book->setReserved(false);
        $book->setReservedAt(null);

        $this->getDoctrine()->getManager()->flush();

        return $this->json($book);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/reserve/{id}', methods: ['PUT'])]
    public function reserve(Book $book) : JsonResponse
    {
        if ($book->getReserved() && $book->getReservation()) {
            throw new \Error('Can not reserve an already reserved book!', 500);
        }
        $book->setReserved(!$book->getReserved());
        $book->setReservedAt(
            null === $book->getReservedAt() ? new \DateTime() : null
        );
        $this->getDoctrine()->getManager()->flush();

        return $this->json($book);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === book.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Book $book, CoverRemove $cover) : JsonResponse
    {
        $cover->remove($book);

        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->json([
            'msg' => 'The book was successfully deleted.',
        ]);
    }
}
