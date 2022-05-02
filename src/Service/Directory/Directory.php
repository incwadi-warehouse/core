<?php

namespace App\Service\Directory;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

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
            if ($file->isFile()) {
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
                'extension' => $item->getExtension(),
                'doc' => $item->getExtension() === 'docx' ? $this->readDoc($item->getPathname()) : null
            ];
        }

        return $items;
    }

    public function mkdir(string $dirname, string $path = './'): bool
    {
        if(!$this->isValidName($dirname)) {
            return false;
        }

        $absolutePath = $this->makeAbsolute($path . '/' . $dirname);

        if (!$this->isInBasePath($absolutePath)) {
            return false;
        }

        $fs = new Filesystem();
        $fs->mkdir($absolutePath);

        return $fs->exists($absolutePath);
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

        if ($fs->exists($absolutePath)) {
            return;
        }

        $fs->touch($absolutePath);
    }

    public function upload(UploadedFile $file, string $filename, string $path = './'): ?File
    {
        if (!$this->isValidName($filename)) {
            return null;
        }

        // 10 mb
        if ($file->getSize() > (10 * 1000 * 1000)) {
            return null;
        }

        $mimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
        ];
        if (!in_array($file->getMimeType(), $mimeTypes)) {
            return null;
        }

        $absolutePath = $this->makeAbsolute($path);

        if (!$this->isInBasePath($absolutePath . '/' . $filename)) {
            return null;
        }

        $fs = new Filesystem();

        if ($fs->exists($absolutePath . '/' . $filename)) {
            return null;
        }

        return $file->move($absolutePath, $filename);
    }

    public function rename(string $orig, string $target, string $path = './'): bool
    {
        if (!$this->isValidName($target)) {
            return false;
        }

        $absolutePathOrig = $this->makeAbsolute($path . '/' . $orig);
        $absolutePathTarget = $this->makeAbsolute($path . '/' . $target);


        if (!$this->isInBasePath($absolutePathOrig) || !$this->isInBasePath($absolutePathTarget)) {
            return false;
        }

        $fs = new Filesystem();

        if (!$fs->exists($absolutePathOrig)) {
            return false;
        }

        $fs->rename($absolutePathOrig, $absolutePathTarget);

        return $fs->exists($absolutePathTarget);
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
        $fs = new Filesystem();

        if (!$fs->exists($filename)) {
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
