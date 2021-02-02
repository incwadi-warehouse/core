<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Service;

use Incwadi\Core\Entity\Book;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CoverUpload
{
    private const SIZES = ['l' => 400, 'm' => 200, 's' => 100];

    private const QUALITY = 75;

    private $path = __DIR__.'/../../data/';

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function upload(Book $book, UploadedFile $file): void
    {
        $image = $this->createImage($file);
        foreach (self::SIZES as $suffix => $size) {
            $this->resize($image, $size, $suffix, $book->getId());
        }
    }

    private function createImage(UploadedFile $file)
    {
        $mimeType = $file->getMimeType();
        if (in_array($mimeType, ['image/jpeg', 'image/jpg'])) {
            return imagecreatefromjpeg($file);
        }
        if ('image/png' === $mimeType) {
            return imagecreatefrompng($file);
        }
        if ('image/webp' === $mimeType) {
            return imagecreatefromwebp($file);
        }
    }

    private function resize($image, int $width, string $suffix, string $id): void
    {
        $filename = $this->path.$id.'-'.$suffix.'.jpg';
        if (file_exists($filename)) {
            throw new \Exception('File does exist already.');
        }
        $quality = imagejpeg(
            imagescale($image, $width),
            $filename,
            self::QUALITY
        );
    }
}
