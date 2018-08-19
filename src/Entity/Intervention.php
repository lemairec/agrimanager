<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Intervention
 *
 * @ORM\Table(name="intervention")
 * @ORM\Entity(repositoryClass="App\Repository\InterventionRepository")
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

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    public $type;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=2048, nullable=true)
     */
    public $comment;

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
    public $materiels   ;

    /**
     * @var float
     *
     * @ORM\Column(name="surface", type="float")
     */
    public $surface;

    public function __construct() {
        $this->parcelles = new ArrayCollection();
        $this->produits = new ArrayCollection();
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

    function get_date(){
        return $this->date->format(' d/m/y');
    }

    function getIsoDate(){
        return $this->date->format(' Y-m-d');
    }
}
