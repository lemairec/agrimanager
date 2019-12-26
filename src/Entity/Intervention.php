<?php

namespace App\Entity;
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
     * @ORM\var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @ORM\var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    public $type;

    /** @ORM\Column(type="float") **/
    public $surface;

    /** @ORM\Column(type="string", length=255, nullable=true) **/
    public $name;

    /** @ORM\Column(type="text", nullable=true) **/
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
    public $materiels;

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
