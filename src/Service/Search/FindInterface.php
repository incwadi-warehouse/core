<?php

namespace App\Service\Search;

interface FindInterface
{
    public function setFields(array $fields): void;

    public function setForcedFilters(array $forcedFilters): void;

    public function find(array $options): array;

    public function count(array $options): int;
}
