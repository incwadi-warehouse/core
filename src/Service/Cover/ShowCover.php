<?php

namespace App\Service\Cover;

use App\Entity\Book;
use Symfony\Component\Filesystem\Filesystem;

class ShowCover extends AbstractCover
{
    public function show(Book $book): array
    {
        return [
            'cover_s' => $this->getCover('s', $book->getId()),
            'cover_m' => $this->getCover('m', $book->getId()),
            'cover_l' => $this->getCover('l', $book->getId()),
        ];
    }

    public function getCoverPath(string $size, string $id): string
    {
        $filesystem = new Filesystem();
        $filename = $this->getPath() . $id . '-' . $size . '.jpg';

        if ($filesystem->exists($filename)) {
            return $filename;
        }

        return __DIR__ . '/none.jpg';
    }

    private function getCover(string $size, string $id): ?string
    {
        $filesystem = new Filesystem();
        $filename = $this->getPath().$id.'-'.$size.'.jpg';

        if ($filesystem->exists($filename)) {
            return 'data:image/jpeg;base64,'.base64_encode(
                file_get_contents($filename)
            );
        }

        return null;
    }
}
