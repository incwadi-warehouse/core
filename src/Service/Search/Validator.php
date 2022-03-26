<?php

namespace App\Service\Search;

class Validator
{
    protected array $fields = [];

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function isValidField(string $field): bool
    {
        return in_array($field, $this->fields);
    }

    public function isValidOperator(string $operator): bool
    {
        return in_array($operator, ['in', 'eq', 'gte', 'gt', 'lte', 'lt', 'null', 'notNull']);
    }
}
