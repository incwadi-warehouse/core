<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateTimeToStringTransformer implements DataTransformerInterface
{
    public function transform($date): mixed
    {
        if (!$date) {
            return null;
        }

        return (string) $date->getTimestamp();
    }

    /**
     * @return \DateTime|\DateTimeImmutable|null
     */
    public function reverseTransform($date): mixed
    {
        return $date ? new \DateTime('@'.$date) : null;
    }
}
