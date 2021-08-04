<?php

namespace App\Service\Portability;

interface ImportInterface
{
    public function import(string $data): array;
}
