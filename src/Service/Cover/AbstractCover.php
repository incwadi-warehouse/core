<?php

namespace App\Service\Cover;

abstract class AbstractCover
{
    /**
     * @var string[]
     */
    protected const SIZES = ['l' => 400, 'm' => 200, 's' => 100];

    /**
     * @var int
     */
    protected const QUALITY = 75;

    private string $path = __DIR__.'/../../../data/cover/';

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
