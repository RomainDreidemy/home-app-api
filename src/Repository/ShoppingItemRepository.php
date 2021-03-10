<?php

namespace App\Repository;

use App\Entity\ShoppingItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShoppingItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShoppingItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShoppingItem[]    findAll()
 * @method ShoppingItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShoppingItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingItem::class);
    }

    public function findByBuyAndList(int $shopping_list_id, bool $buy)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.shoppingList', 'l')
            ->where('l.id = :shopping_list_id')
            ->andWhere('i.buy = :buy')
            ->setParameter('shopping_list_id', $shopping_list_id)
            ->setParameter('buy', $buy)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return ShoppingItemService[] Returns an array of ShoppingItemService objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShoppingItemService
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
