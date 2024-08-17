<?php

namespace App\Repository\Iot;

use App\Entity\Iot\Iot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Iot>
 *
 * @method Iot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Iot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Iot[]    findAll()
 * @method Iot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Iot::class);
    }

    public function save(Iot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Iot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOrCreate($company, $name){
        $iot = $this->createQueryBuilder('s')
            ->andWhere('s.company = :company')
            ->setParameter('company', $company)
            ->andWhere('s.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;


        if($iot == NULL){
            $iot = new Iot();
            $iot->company = $company;
            $iot->name = $name;

            $this->save($iot, true);
        }

        return $iot;
    }

    function getAllForCompany($company){
        return $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->orderBy('p.label')
            ->setParameter('company', $company)
            ->getQuery()->getResult();
    }


//    /**
//     * @return Iot[] Returns an array of Iot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Iot
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
