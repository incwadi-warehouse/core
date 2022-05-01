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
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/directory')]
class DirectoryController extends AbstractApiController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(Directory $directory, Request $request) : JsonResponse
    {
        $elements = $directory->list($request->query->get('dir'));

        return $this->json($elements);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/cover/{book}', methods: ['POST'])]
    public function cover(Request $request, Book $book, UploadCover $cover) : JsonResponse
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

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Directory $directory, Request $request): JsonResponse
    {
        $directory = new Directory();
        $directory->mkdir(
            $request->query->get('name'),
            $request->query->get('path')
        );

        return $this->json(['msg'=>'SUCCESS']);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/upload', methods: ['POST'])]
    public function upload(Directory $directory, Request $request): JsonResponse
    {
        $file = $directory->upload(
            $request->files->get('image'),
            $request->files->get('image')->getClientOriginalName(),
            $request->query->get('dir'),
        );

        if ($file instanceof File) {
            return $this->json(['msg'=> 'SUCCESS']);
        }

        throw new \Error('Could not upload image.');
    }

    // /**
    //  * @Route("/{directory}", methods={"PUT"})
    //  * @Security("is_granted('ROLE_USER')")
    //  */
    // public function edit(Request $request, Directory $directory,ManagerRegistry $manager): JsonResponse
    // {
    //     $form = $this->createForm(DirectoryType::class, $directory);

    //     $form->submit(
    //         $this->submitForm($request)
    //     );
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $em = $manager->getManager();
    //         $em->flush();

    //         return $this->setResponse()->single($this->fields, $directory);
    //     }

    //     return $this->setResponse()->invalid();
    // }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['DELETE'])]
    public function delete(Directory $directory, Request $request): JsonResponse
    {
        $directory->remove(
            $request->query->get('name'),
            $request->query->get('path')
        );

        return $this->setResponse()->deleted();
    }
}
