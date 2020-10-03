<?php

namespace App\Repository;

use App\Entity\PhotoPackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhotoPackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoPackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoPackage[]    findAll()
 * @method PhotoPackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoPackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoPackage::class);
    }

    public function findAllNotCustom(): array
    {
        $qb = $this->createQueryBuilder('pkg');
        $qb->where('pkg.name != :name')
            ->setParameter('name', 'Autre');

        return $qb->getQuery()
            ->getResult();
    }
}
