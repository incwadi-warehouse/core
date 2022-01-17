<?php

namespace App\Controller;

use Baldeweg\Bundle\ApiBundle\AbstractApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Directory\Directory;
use App\Service\Cover\UploadCover;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Path;

/**
 * @Route("/api/directory")
 */
class DirectoryController extends AbstractApiController
{
    private $fields = [];

    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(Directory $directory, Request $request): JsonResponse
    {
        $elements = $directory->list($request->query->get('dir'));
        return $this->json($elements);
    }

    /**
     * @Route("/cover/{book}", methods={"POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function cover(Directory $directory, Request $request, Book $book, UploadCover $cover): JsonResponse
    {
        $content = json_decode($request->getContent());
        $absolutePath = Path::makeAbsolute($content->url, __DIR__ . '/../../data/directory/');

        if (!preg_match('#^'. Path::canonicalize(__DIR__ . '/../../data/directory/').'#', $absolutePath)) {
            throw $this->createNotFoundException();
        }

        $file = new File(__DIR__ . '/../../data/directory/'. $content->url);
        $file->openFile();

        $cover->upload($book, $file);

        return $this->json($book);
    }

    // /**
    //  * @Route("/{directory}", methods={"GET"})
    //  * @Security("is_granted('ROLE_USER')")
    //  */
    // public function show(Directory $directory): JsonResponse
    // {
    //     return $this->setResponse()->single($this->fields, $directory);
    // }

    // /**
    //  * @Route("/new", methods={"POST"})
    //  * @Security("is_granted('ROLE_USER')")
    //  */
    // public function new(Request $request): JsonResponse
    // {
    //     $directory = new Directory();
    //     $form = $this->createForm(DirectoryType::class, $directory);

    //     $form->submit(
    //         $this->submitForm($request)
    //     );
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($directory);
    //         $em->flush();

    //         return $this->setResponse()->single($this->fields, $directory);
    //     }

    //     return $this->setResponse()->invalid();
    // }

    // /**
    //  * @Route("/{directory}", methods={"PUT"})
    //  * @Security("is_granted('ROLE_USER')")
    //  */
    // public function edit(Request $request, Directory $directory): JsonResponse
    // {
    //     $form = $this->createForm(DirectoryType::class, $directory);

    //     $form->submit(
    //         $this->submitForm($request)
    //     );
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $em = $this->getDoctrine()->getManager();
    //         $em->flush();

    //         return $this->setResponse()->single($this->fields, $directory);
    //     }

    //     return $this->setResponse()->invalid();
    // }

    // /**
    //  * @Route("/{directory}", methods={"DELETE"})
    //  * @Security("is_granted('ROLE_USER')")
    //  */
    // public function delete(Directory $directory): JsonResponse
    // {
    //     $em = $this->getDoctrine()->getManager();
    //     $em->remove($directory);
    //     $em->flush();

    //     return $this->setResponse()->deleted();
    // }
}
