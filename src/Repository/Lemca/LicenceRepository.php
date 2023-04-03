<?php

namespace App\Repository\Lemca;

use App\Entity\Lemca\Licence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Licence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Licence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Licence[]    findAll()
 * @method Licence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LicenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Licence::class);
    }


    function zz_ch2($un,$deux) {
        $dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $zero="";
        echo("\n\nzz_ch2".strlen($dict)."\n").
        $rlLong=strlen($un);
        $rlCrypt=strlen($deux);
        $rlVar0 = 0;
        if($rlLong>0 && $rlCrypt>0){
            $rlPos=0;
            for($i=0;$i<$rlLong;$i++){
                $rlVar1=strpos($dict, $un[$i]);
                $rlVar2=strpos($dict, $deux[$rlPos]);
                $rlVarP=($rlVar0+$rlVar1+$rlVar2)%strlen($dict);
                echo("$i, "." $rlVar0 $rlVar1 $rlVar2 $rlVarP\n");
                $rlVar0 = ($rlVarP+$rlVar0)%strlen($dict);
                $zero=$zero.$dict[$rlVarP];
                $rlPos=$rlPos+1;
                if($rlPos>$rlCrypt-1) $rlPos=0;
            }
        }
        return $zero;
     }

    function zz_dech2($un,$deux) {
        $dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $zero="";
        echo("\n\nzzdech2 ".strlen($dict)."\n").
        $rlLong=strlen($un);
        $rlCrypt=strlen($deux);
        $rlVar0 = 0;
        if($rlLong>0 && $rlCrypt>0){
            $rlPos=0;
            for($i=0;$i<$rlLong;$i++)
                {
                $rlVar1=strpos($dict, $un[$i]);
                $rlVar2=strpos($dict, $deux[$rlPos]);
                $rlVarP=($rlVar1-$rlVar2-$rlVar0)%strlen($dict);
                echo("$i, "." $rlVar0 $rlVar1 $rlVar2 $rlVarP\n");
                $rlVar0 = ($rlVar0+$rlVar1)%strlen($dict);
                $zero=$zero.$dict[$rlVarP];
                $rlPos=$rlPos+1;
                if($rlPos>$rlCrypt-1) $rlPos=0;
            }
        }
        return $zero;
    }






    public function save($licence)
    {
        $key="WTHGDFHKKHUIKHG";


        $licence->licence_decode = "";

        $len = strlen($licence->panel);
        if($len <= 6){
            $licence->licence_decode = $licence->licence_decode.$licence->panel;
            for($i =$len; $i < 6; ++$i){
                $licence->licence_decode = $licence->licence_decode." ";
            }
        } else {
            $licence->licence_decode = $licence->licence_decode.substr($licence->panel,0,3);
            $licence->licence_decode = $licence->licence_decode.substr($licence->panel,$len-3,3);
        }

        $len = strlen($licence->boitier);
        if($len <= 6){
            $licence->licence_decode = $licence->licence_decode.$licence->boitier;
            for($i =$len; $i < 6; ++$i){
                $licence->licence_decode = $licence->licence_decode." ";
            }
        } else {
            $licence->licence_decode = $licence->licence_decode.substr($licence->boitier,0,3);
            $licence->licence_decode = $licence->licence_decode.substr($licence->boitier,$len-3,3);
        }
        $licence->licence_decode = $licence->licence_decode."LEMCA1";

        $licence->date_create = new \DateTime();

        $licence->licence = $this->zz_ch2($licence->licence_decode, $key);

        $em = $this->getEntityManager();
        $em->persist($licence);
        $em->flush();
    }

    public function getAll()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
