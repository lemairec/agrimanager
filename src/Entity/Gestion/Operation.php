<?php

namespace App\Entity\Gestion;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operation
 *
 * @ORM\Entity(repositoryClass="App\Repository\Gestion\OperationRepository")
 * @ORM\Table(name="operation")
 */
class Operation
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\Gestion\Ecriture", mappedBy="operation",cascade={"persist"})
    */
    public $ecritures = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gestion\FactureFournisseur")
     * @ORM\JoinColumn(nullable=true)
     */
    public $facture;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gestion\Emprunt")
     * @ORM\JoinColumn(nullable=true)
     */
    public $emprunt;

    public function getSumEcriture($compte_name){
        $res = 0;
        foreach($this->ecritures as $e){
            if($e->compte->name == $compte_name){
                $res += $e->value;
            }
        }
        return $res;
    }

    public function getTotalD(){
        $res = 0;
        foreach($this->ecritures as $e){
            if($e->value > 0){
                $res += $e->value;
            }
        }
        return $res;
    }

    public function getTotalC(){
        $res = 0;
        foreach($this->ecritures as $e){
            if($e->value < 0){
                $res += $e->value;
            }
        }
        return $res;
    }

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }
}
