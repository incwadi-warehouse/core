<?php

namespace Incwadi\Core\Service\Portability;

interface ExportInterface
{
    public function export(array $data): string;
}
