<?php

namespace App\Form\DataTransformer;

use App\Entity\PhotoPackage;
use App\Repository\PhotoPackageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PhotoPackageTransformer implements DataTransformerInterface
{
    private PhotoPackageRepository $photoPackageRepository;
    private EntityManagerInterface $em;

    public function __construct(PhotoPackageRepository $photoPackageRepository, EntityManagerInterface $em)
    {
        $this->photoPackageRepository = $photoPackageRepository;
        $this->em = $em;
    }

    public function transform($value): PhotoPackage
    {
        if (is_string($value) && Uuid::isValid($value)) {
            $package = $this->photoPackageRepository->find($value);

            if (null !== $package) {
                return $package;
            }
        } else if ($value === 'other') {
            $pkg = (new PhotoPackage())
                ->setName('Autre')
                ->setQuantity(4);

            $this->em->persist($pkg);
            $this->em->flush();
        }

        throw new TransformationFailedException(sprintf('Photo package with id %s does not exist.', $value));
    }

    public function reverseTransform($value): PhotoPackage
    {
        return $this->photoPackageRepository->find($value);
    }
}