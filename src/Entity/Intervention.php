<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Intervention
 *
 * @ORM\Entity(repositoryClass="App\Repository\InterventionRepository")
 * @ORM\Table(name="intervention")
 */
class Intervention
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    /** @ORM\Column(type="datetime") **/
    public $datetime;
	
	/** @ORM\Column(type="datetime", nullable=true) **/
    public $datetimeEnd;

	/** @ORM\Column(type="time", nullable=true) **/
    public $duration;

    /** @ORM\Column(name="type", type="string", length=255) **/
    public $type;

    /** @ORM\Column(type="float") **/
    public $surface;

    /** @ORM\Column(type="text", length=255, nullable=true) **/
    public $name;

    /** @ORM\Column(type="text", nullable=true) **/
    public $comment;

    /** @ORM\Column(type="text", nullable=true, name="meteoJson") **/
    public $meteoJson;

    /** @ORM\Column(type="string", length=255, nullable=true) **/
    public $migration;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\InterventionParcelle", mappedBy="intervention",cascade={"persist"})
    */
    public $parcelles;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\InterventionProduit", mappedBy="intervention",cascade={"persist"})
    */
    public $produits;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\InterventionMateriel", mappedBy="intervention",cascade={"persist"})
    */
    public $materiels;

     /**
    * @ORM\OneToMany(targetEntity=App\Entity\InterventionRecolte::class, mappedBy="intervention",cascade={"persist"})
    * @ORM\OrderBy({"datetime" = "ASC"})
 
    */
    public $recoltes;

    public function getDatetimeStr(){
        return $this->datetime->format("d/m/y");
    }

    public function getDatetimeStr2(){
        return $this->datetime->format("d/m/Y");
    }

    function getPriceHa(){
        $res = 0;
        foreach($this->produits as $p){
            $res += $p->getPriceHa();
        }
        return $res;
    }

    function getMainCulture(){
        $culture = NULL;
        $oneCulture = true;
        foreach($this->parcelles as $p){
            if($culture == NULL){
                $culture = $p->parcelle->culture;
            }
            if($culture->id != $p->parcelle->culture->id){
                $oneCulture = false;
            }
        }
        if($oneCulture){
            return $culture;
        }
        return NULL;
    }

    function getColor(){
        $culture = $this->getMainCulture();
        if($culture){
            return $culture->color;
        }
        return "#ffffff";
    }

    function getTypeCalendar(){
        return str_replace('&', '+', $this->type);
    }

    public function getIsoDate(){
        return $this->datetime->format(' Y-m-d');
    }
}
