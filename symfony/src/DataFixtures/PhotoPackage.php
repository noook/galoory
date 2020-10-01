<?php

namespace App\DataFixtures;

use App\Entity\PhotoPackage as EntityPhotoPackage;
use App\Repository\PhotoPackageRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PhotoPackage extends Fixture implements FixtureGroupInterface
{
    private PhotoPackageRepository $photoPackageRepository;

    public function __construct(PhotoPackageRepository $photoPackageRepository)
    {
        $this->photoPackageRepository = $photoPackageRepository;   
    }
    public function load(ObjectManager $manager)
    {
        $this->photoPackageRepository
            ->createQueryBuilder('pp')
            ->delete()
            ->getQuery()
            ->getSingleScalarResult();

        $packages = [
            (new EntityPhotoPackage())
                ->setName('Découverte')
                ->setQuantity(6),
            (new EntityPhotoPackage())
                ->setName('Avantage')
                ->setQuantity(10),
            (new EntityPhotoPackage())
                ->setName('Prestige')
                ->setQuantity(15),
            (new EntityPhotoPackage())
                ->setName('Duo')
                ->setQuantity(15),
        ];

        foreach ($packages as $package) {
            $manager->persist($package);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['photoshoot-package'];
    }
}
