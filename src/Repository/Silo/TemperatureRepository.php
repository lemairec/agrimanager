<?php

namespace App\Repository\Silo;

use App\Entity\Silo\Temperature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SiloTemperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiloTemperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiloTemperature[]    findAll()
 * @method SiloTemperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemperatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Temperature::class);
    }

    function roundTo15Min(\DateTime $dt) {
        $s = 15 * 60;
        $dt->setTimestamp($s * (int) floor($dt->getTimestamp() / $s));
        return $dt;
    }

    function roundTo60Min(\DateTime $dt) {
        $s = 15 * 60;
        $dt->setTimestamp($s * (int) floor($dt->getTimestamp() / $s));
        return $dt;
    }

    function getAllForBalise($balise){
        for($i = 0; $i<200; ++$i){
            $res = $this->createQueryBuilder('p')
                ->where('p.balise = :balise')
                ->andWhere('p.rounded_datetime is NULL')
                ->orderBy('p.datetime', 'ASC')
                ->setParameter('balise', $balise)
                ->getQuery()->getResult();
            if(count($res) > 1){
                $this->addTemperature($res[0]);
            } else {
                break;
            }
        }

        return $this->createQueryBuilder('p')
            ->where('p.balise = :balise')
            ->orderBy('p.datetime', 'DESC')
            ->setParameter('balise', $balise)
            ->getQuery()->getResult();
    }

    function addTemperature($t){
        $em = $this->getEntityManager();
        
        $rounded_datetime = new \DateTime($t->datetime->format("Y-m-d H:i:s"));
        $rounded_datetime = $this->roundTo60Min($rounded_datetime);
        $t->rounded_datetime = $rounded_datetime;
        $olds = $this->createQueryBuilder('p')
            ->where('p.balise = :balise')
            ->andWhere('p.rounded_datetime = :rounded_datetime')
            ->setParameter('balise', $t->balise)
            ->setParameter('rounded_datetime', $rounded_datetime)
            ->getQuery()->getResult();
        $old = NULL;
        if(count($olds)>0){
            $old = $olds[0];
        }
        if($old == NULL){
            $em->persist($t);
            $em->flush();
        } else {
            $old_t = $old->datetime->getTimestamp()-$old->rounded_datetime->getTimestamp();
            $temp_t = $t->datetime->getTimestamp()-$t->rounded_datetime->getTimestamp();
            if($temp_t < $old_t){
                $em->remove($old);
                //print("delete ".$old->id);
                $em->persist($t);
            } else {
                if($t->id){
                    $em->remove($t);
                }
                //print("delete ".$t->id);
            }
            $em->flush();
        }

    }

    // /**
    //  * @return SiloTemperature[] Returns an array of SiloTemperature objects
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
    public function findOneBySomeField($value): ?SiloTemperature
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
