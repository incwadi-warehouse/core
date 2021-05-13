<?php

namespace Incwadi\Core\Service\Portability;

interface ImportInterface
{
    public function import(string $data): array;
}
