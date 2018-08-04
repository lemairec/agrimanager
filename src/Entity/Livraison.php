<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Livraison
 *
 * @ORM\Table(name="livraison")
 * @ORM\Entity(repositoryClass="App\Repository\LivraisonRepository")
 */
class Livraison
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Campagne")
     * @ORM\JoinColumn(nullable=false)
     */
    public $campagne;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    public $date;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable=true))
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicule", type="string", length=255,nullable=true))
     */
    public $vehicule;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255,nullable=true))
     */
    public $espece;

    /**
     * @var float
     *
     * @ORM\Column(type="float",nullable=true)
     */
    public $poid_total;

    /**
     * @var float
     *
     * @ORM\Column(type="float",nullable=true)
     */
    public $tare;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $humidite;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $impurete;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $ps;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $proteine;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $calibrage;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $poid_norme;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Parcelle")
     * @ORM\JoinColumn(name="parcelle_id",nullable=true)
     */
    public $parcelle;

    function getDateStr(){
        return $this->date->format('d/m/y');
    }

    static function getStaticCarateristiques($humidite, $ps, $proteine, $calibrage, $impurete){
        $res = "";
        if($humidite){
            $res = $res."HUM ".number_format($humidite, 2);
        }
        if($ps){
            if(strlen($res)>0){
                $res = $res.", ";
            }
            $res = $res."PS ".number_format($ps, 2);
        }
        if($proteine){
            if(strlen($res)>0){
                $res = $res.", ";
            }
            $res = $res."PROT ".number_format($proteine, 2);
        }
        if($calibrage){
            if(strlen($res)>0){
                $res = $res.", ";
            }
            $res = $res."CAL ".number_format($calibrage, 2);
        }
        if($impurete){
            if(strlen($res)>0){
                $res = $res.", ";
            }
            $res = $res."IMP ".number_format($impurete, 2);
        }
        return $res;
    }

    function getCarateristiques(){
        return $this->getStaticCarateristiques($this->humidite, $this->ps, $this->proteine, $this->calibrage, $this->impurete);
    }
}
