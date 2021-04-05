<?php

namespace App\Repository;

use App\Entity\Chore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chore|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chore|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chore[]    findAll()
 * @method Chore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chore::class);
    }

    // /**
    //  * @return Chore[] Returns an array of Chore objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Chore
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
