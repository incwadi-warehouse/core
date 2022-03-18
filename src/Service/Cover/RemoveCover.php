<?php

namespace App\Service\Cover;

use App\Entity\Book;
use Symfony\Component\Filesystem\Filesystem;

class RemoveCover extends AbstractCover
{
    public function remove(Book $book): void
    {
        $filesystem = new Filesystem();

        foreach (array_keys(self::SIZES) as $suffix) {
            $filename = $this->getPath().$book->getId().'-'.$suffix.'.jpg';
            if ($filesystem->exists($filename)) {
                $filesystem->remove($filename);
            }
        }
    }
}
