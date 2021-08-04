<?php

namespace App\Service\Cover;

use App\Entity\Book;

class CoverShow
{
    private $path = __DIR__.'/../../../data/';

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function show(Book $book): array
    {
        return [
            'cover_s' => $this->getCover('s', $book->getId()),
            'cover_m' => $this->getCover('m', $book->getId()),
            'cover_l' => $this->getCover('l', $book->getId()),
        ];
    }

    private function getCover(string $size, string $id): ?string
    {
        $filename = $this->path.$id.'-'.$size.'.jpg';
        if (is_file($filename)) {
            return 'data:image/jpeg;base64,'.base64_encode(
                file_get_contents($filename)
            );
        }

        return null;
    }
}
