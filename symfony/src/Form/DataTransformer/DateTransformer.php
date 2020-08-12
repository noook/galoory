<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateTransformer implements DataTransformerInterface
{
    public function transform($value): \DateTime
    {
        return new \DateTime($value);
    }

    public function reverseTransform($value): string
    {
        return \DateTime::createFromFormat(DATE_ATOM, $value);
    }
}
