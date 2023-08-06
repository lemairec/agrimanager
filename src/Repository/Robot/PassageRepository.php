<?php

namespace App\Repository\Robot;

use App\Entity\Robot\Passage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Passage>
 *
 * @method Passage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Passage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Passage[]    findAll()
 * @method Passage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PassageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Passage::class);
    }

    public function save(Passage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Passage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByRobot(Robot $robot)
    {
        return $this->createQueryBuilder('p')
                    ->andWhere('p.robot = :robot')
                    ->setParameter('robot', $robot)
                    ->orderBy('p.id', 'DESC')
                    ->setMaxResults(100)
                    ->getQuery()
                    ->getResult();
    }

//    /**
//     * @return Passage[] Returns an array of Passage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Passage
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
