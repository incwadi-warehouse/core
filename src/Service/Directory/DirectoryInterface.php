<?php

namespace App\Service\Directory;

interface DirectoryInterface
{
    public function setBasePath(string $path): void;

    public function getBasePath(): string;

    public function mkdir(string $dirname, string $path = './'): void;

    public function touch(string$filename, string $path = './'): void;

    public function list(string $dir = '/'): array;

    public function remove(string$name, string $path = './'): void;
}
