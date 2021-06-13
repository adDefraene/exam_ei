<?php

namespace App\Repository;

use App\Entity\Order;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getTodayOrders()
    {
        $today = new DateTime();
        $ordered = "ORDERED";

        return $this->createQueryBuilder('o')
            ->andWhere('o.date > :val')
            ->andWhere('o.state = :state')
            ->setParameter('val', $today)
            ->setParameter('state', $ordered)
            ->orderBy('o.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneById($id): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
