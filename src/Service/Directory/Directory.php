<?php

namespace App\Service\Directory;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Path;

class Directory implements DirectoryInterface
{
    private string $basePath = __DIR__ . '/../../../data/directory';

    public function __construct()
    {
        $fs = new Filesystem();

        if (!$fs->exists($this->getBasePath())) {
            $fs->mkdir($this->getBasePath());
        }
    }

    public function setBasePath(string $path): void
    {
        $this->basePath = $path;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function mkdir(string $dirname, string $path = './'): void
    {
        if(!$this->isValidName($dirname)) {
            return;
        }

        $absolutePath = $this->makeAbsolute($path . '/' . $dirname);

        if (!$this->isInBasePath($absolutePath)) {
            return;
        }

        $fs = new Filesystem();
        $fs->mkdir($absolutePath);
    }

    public function touch(string $filename, string $path = './'): void
    {
        if (!$this->isValidName($filename)) {
            return;
        }


        $absolutePath = $this->makeAbsolute($path . '/' . $filename);

        if (!$this->isInBasePath($absolutePath)) {
            return;
        }

        $fs = new Filesystem();
        $fs->touch($absolutePath);
    }

    public function list(string $dir = './'): array
    {
        $absolutePath = $this->makeAbsolute($dir);

        if (!is_dir($absolutePath)) {
            return [];
        }

        if (!$this->isInBasePath($absolutePath)) {
            return [];
        }

        $finder = new Finder();
        $finder->in($absolutePath)->depth('== 0');

        $items = [];
        foreach ($finder as $item) {
            $items[] = [
                'name' => $item->getFilename(),
                'path' => $item->getRelativePathname(),
                'isFile' => $item->isFile(),
                'isDir' => $item->isDir(),
                'size' => $item->getSize()
            ];
        }

        return $items;
    }

    public function remove(string $name, string $path = './'): void
    {
        if (!$this->isValidName($name)) {
            return;
        }


        $absolutePath = $this->makeAbsolute($path . '/' . $name);

        if (!$this->isInBasePath($absolutePath)) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($absolutePath);
    }

    private function isValidName(string $name): bool
    {
        return strlen($name) >= 1 && !preg_match('#[^a-zA-Z0-9-_\.]#', $name);
    }

    private function makeAbsolute(string $path): string
    {
        return Path::makeAbsolute($path, $this->getBasePath());
    }

    private function isInBasePath(string $path): bool
    {
        return (bool)preg_match('#^' . Path::canonicalize($this->getBasePath()) . '#', $path);
    }
}
