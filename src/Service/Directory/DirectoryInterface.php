<?php

namespace App\Service\Directory;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

interface DirectoryInterface
{
    public function setBasePath(string $path): void;

    public function getBasePath(): string;

    public function list(string $dir = '/'): array;

    public function mkdir(string $dirname, string $path = './'): bool;

    public function touch(string$filename, string $path = './'): void;

    public function upload(UploadedFile $file, string $filename, string $path = './'): ?File;

    public function rename(string $orig, string $target, string $path = './'): bool;

    public function remove(string$name, string $path = './'): void;
}
