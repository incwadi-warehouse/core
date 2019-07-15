<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Util;

use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class Export implements ExportInterface
{
    public function export(array $data): string
    {
        $formatDate = function ($object) {
            return $object instanceof \DateTime ? $object->format(\DateTime::ISO8601) : '';
        };
        $formatAuthor = function ($object) {
            return $object instanceof \Incwadi\Core\Entity\Author ? ['firstname' => $object->getFirstname(), 'lastname' => $object->getLastname()] : ['firstname' => null, 'lastname' => null];
        };
        $formatLendTo = function ($object) {
            return $object instanceof \Incwadi\Core\Entity\Customer ? $object->getName() : null;
        };
        $formatBranch = function ($object) {
            return $object instanceof \Incwadi\Core\Entity\Branch ? $object->getName() : null;
        };
        $formatGenre = function ($object) {
            return $object instanceof \Incwadi\Core\Entity\Genre ? $object->getName() : null;
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'added' => $formatDate,
                'lendOn' => $formatDate,
                'author' => $formatAuthor,
                'lendTo' => $formatLendTo,
                'branch' => $formatBranch,
                'genre' => $formatGenre
            ],
        ];

        $serializer = new Serializer(
            [new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext)],
            [new CsvEncoder()]
        );

        return $serializer->serialize($data, 'csv', [
            'csv_delimiter' => ';',
            'ignored_attributes' => ['id']
        ]);
    }
}
