<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function getAll()
    {
        return $this->createQueryBuilder('d')
            ->addOrderBy('d.directory', 'ASC')
            ->addOrderBy('d.date', 'ASC')
            ->addOrderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    function getAllforCompany($company){
        $query = $this->createQueryBuilder('d')
            ->where('d.company = :company')
            ->setParameter('company', $company)
            ->addOrderBy('d.directory', 'ASC')
            ->addOrderBy('d.date', 'ASC')
            ->addOrderBy('d.name', 'ASC')
            ->getQuery();

        return $query->getResult();
    }


    function getAllForExport($company){
        $query = $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->andWhere('p.dateExport is NULL')
            ->setParameter('company', $company)
            ->orderBy('p.date', 'DESC')
            ->getQuery();

            return $query->getResult();
    }
}
