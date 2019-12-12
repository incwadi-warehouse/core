<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Util;

interface ExportInterface
{
    public function export(array $data): string;
}
