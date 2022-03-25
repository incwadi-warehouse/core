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

        $filter = function (\SplFileInfo $file) {
            if($file->isFile()) {
                return in_array($file->getExtension(), ['docx', 'JPG', 'jpg', 'webp']);
            }
        };

        $finder = new Finder();
        $finder->in($absolutePath)->filter($filter)->depth('== 0')->sortByName()->sortByType();

        $items = [];
        $items['details']['parent'] = [
            'name' => '../',
            'path' => Path::makeRelative($absolutePath . '/../', $this->basePath),
        ];
        $items['details']['current'] = [
            'name' => './',
            'path' => Path::makeRelative($absolutePath, $this->basePath),
        ];
        foreach ($finder as $item) {
            $items['contents'][] = [
                'name' => $item->getFilename(),
                'path' => Path::makeRelative($item->getPathname(), $this->basePath),
                'isFile' => $item->isFile(),
                'isDir' => $item->isDir(),
                'size' => $item->getSize(),
                'doc' => $item->getExtension() === 'docx' ? $this->readDoc($item->getPathname()) : null
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

    private function readDoc($filename): ?string
    {
        if(!file_exists($filename)) {
            return null;
        }

        $zip = new \ZipArchive();
        $zip->open($filename);

        for ($i = 0; $i < $zip->numFiles; ++$i) {
            if ('word/document.xml' === $zip->getNameIndex($i)) {
                return trim(
                    strip_tags(
                        $zip->getFromIndex($i)
                    )
                );
            }
        }

        return null;
    }
}
