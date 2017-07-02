<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operation
 *
 * @ORM\Table(name="operation")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\OperationRepository")
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
    * @ORM\OneToMany(targetEntity="GestionBundle\Entity\Ecriture", mappedBy="operation",cascade={"persist"})
    */
    public $ecritures;

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
