<?php

namespace App\Service\Cover;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class UploadCover extends AbstractCover
{
    public function __construct()
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->getPath())) {
            $filesystem->mkdir($this->getPath());
        }
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
        $filesystem = new Filesystem();
        $filename = $this->getPath().$id.'-'.$suffix.'.jpg';

        if ($filesystem->exists($filename)) {
            throw new \Exception('File does exist already.');
        }
        imagejpeg(
            imagescale($image, $width),
            $filename,
            self::QUALITY
        );
    }
}
