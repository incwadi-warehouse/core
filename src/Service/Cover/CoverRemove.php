<?php

namespace Incwadi\Core\Service\Cover;

use Incwadi\Core\Entity\Book;

class CoverRemove
{
    /**
     * @var string[]
     */
    private const SIZES = ['s', 'm', 'l'];

    private $path = __DIR__.'/../../../data/';

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
