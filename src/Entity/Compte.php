<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte
 *
 * @ORM\Table(name="compte")
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 */
class Compte
{
    /**
     * @var guid
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campagne")
     * @ORM\JoinColumn(nullable=true)
     */
    public $campagne;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=255)
     */
    public $shortName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $type;

    /**
     * @var float
     *
     * @ORM\Column(name="previsionnel", type="float")
     */
    public $previsionnel = 0;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\Ecriture", mappedBy="compte",cascade={"persist"})
    */
    public $ecritures;

    public function getPrice ( ){
        $res = 0;
        foreach($this->ecritures as $e){
            $res += $e->value;
        }
        return $res;
    }

    public function getPriceNull(){
        $res = 0;
        foreach($this->ecritures as $e){
            if($e->campagne==null){
                $res += $e->value;
            }
        }
        return $res;
    }

    public function getPriceCampagne($campagne){
        $res = 0;
        foreach($this->ecritures as $e){
            if($e->campagne==$campagne){
                $res += $e->value;
            }
        }
        return $res;
    }

    public function __toString ( ){
        return $this->name;
    }
}
