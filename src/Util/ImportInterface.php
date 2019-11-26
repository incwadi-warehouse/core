<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Util;

interface ImportInterface
{
    public function import(string $data): array;
}
