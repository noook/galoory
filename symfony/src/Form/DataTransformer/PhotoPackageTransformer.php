<?php

namespace App\Form\DataTransformer;

use App\Entity\PhotoPackage;
use App\Repository\PhotoPackageRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PhotoPackageTransformer implements DataTransformerInterface
{
    private PhotoPackageRepository $photoPackageRepository;

    public function __construct(PhotoPackageRepository $photoPackageRepository)
    {
        $this->photoPackageRepository = $photoPackageRepository;
    }

    public function transform($value): PhotoPackage
    {
        if (is_string($value) && Uuid::isValid($value)) {
            $package = $this->photoPackageRepository->find($value);

            if (null !== $package) {
                return $package;
            }
        }

        throw new TransformationFailedException(sprintf('Photo package with id %s does not exist.', $value));
    }

    public function reverseTransform($value): PhotoPackage
    {
        return $this->photoPackageRepository->find($value);
    }
}