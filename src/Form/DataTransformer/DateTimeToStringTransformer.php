<?php

namespace Incwadi\Core\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateTimeToStringTransformer implements DataTransformerInterface
{
    public function transform($date): ?string
    {
        if (!$date) {
            return null;
        }

        return (string) $date->getTimestamp();
    }

    public function reverseTransform($date): ?\DateTime
    {
        return $date ? new \DateTime('@'.$date) : null;
    }
}
