<?php

namespace App\Repository\Robot;

use App\Entity\Robot\Order;
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

    public function getLastForRobot($robot)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.robot = :robot')
            ->andWhere('o.status = :status')
            ->setParameter('status', "wait")
            ->setParameter('robot', $robot)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getDoingForRobot($robot)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.robot = :robot')
            ->andWhere('o.status = :status')
            ->setParameter('robot', $robot)
            ->setParameter('status', "doing")
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getForRobot($robot)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.robot = :robot')
            ->setParameter('robot', $robot)
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function cancelAllOrders($robot)
    {
        $res = $this->createQueryBuilder('o')
            ->andWhere('o.robot = :robot')
            ->andWhere('o.status != :status')
            ->setParameter('robot', $robot)
            ->setParameter('status', "done")
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
        $em = $this->getEntityManager();
        foreach ($res as $o) {
            $o->status = "cancel";
            $em->persist($o);
            $em->flush();
        }
    }

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
