<?php

namespace App\Repository;

use App\Entity\Chore;
use App\Entity\User;
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

    public function findByHomeId(int $home_id): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.home', 'h')
            ->andWhere('h.id = :id')
            ->setParameter('id', $home_id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWithoutUser(int $home_id): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.home', 'h')
            ->leftJoin('c.user', 'u')
            ->where('u.id is null')
            ->andWhere('h.id = :id')
            ->setParameter('id', $home_id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function removeUser(int $home_id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE chore SET user_id = null WHERE home_id = :home_id';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['home_id' => $home_id]);
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
