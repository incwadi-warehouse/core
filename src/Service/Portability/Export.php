<?php

namespace App\Service\Portability;

use App\Entity\Author;
use App\Entity\Branch;
use App\Entity\Genre;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/*
 * Deprecated
 */
class Export implements ExportInterface
{
    public function export(array $data): string
    {
        $formatDate = function ($object) {
            return $object instanceof \DateTime ? $object->format(\DateTime::ISO8601) : '';
        };
        $formatAuthor = function ($object) {
            return $object instanceof Author ? ['firstname' => $object->getFirstname(), 'surname' => $object->getSurname()] : ['firstname' => null, 'surname' => null];
        };
        $formatBranch = function ($object) {
            return $object instanceof Branch ? $object->getName() : null;
        };
        $formatGenre = function ($object) {
            return $object instanceof Genre ? $object->getName() : null;
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'added' => $formatDate,
                'author' => $formatAuthor,
                'branch' => $formatBranch,
                'genre' => $formatGenre,
                'soldOn' => $formatDate,
            ],
        ];

        $serializer = new Serializer(
            [new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext)],
            [new CsvEncoder()]
        );

        return $serializer->serialize($data, 'csv', [
            'csv_delimiter' => ';',
            'ignored_attributes' => ['id'],
        ]);
    }
}
