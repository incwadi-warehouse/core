<?php

namespace App\Service\Cover;

use App\Entity\Book;

class CoverRemove
{
    /**
     * @var string[]
     */
    private const SIZES = ['s', 'm', 'l'];

    private $path = __DIR__.'/../../../data/cover/';

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function remove(Book $book): void
    {
        foreach (self::SIZES as $size) {
            $filename = $this->path.$book->getId().'-'.$size.'.jpg';
            if (is_file($filename)) {
                unlink($filename);
            }
        }
    }
}
