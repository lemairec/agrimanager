<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Intervention
 *
 * @Table(name="intervention")
 * @Entity(repositoryClass="App\Repository\InterventionRepository")
 */
class Intervention
{
    /**
     * @var guid
     *
     * @Column(name="id", type="guid")
     * @Id
     * @GeneratedValue(strategy="UUID")
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="App\Entity\Campagne")
     * @JoinColumn(nullable=false)
     */
    public $campagne;

    /**
     * @ManyToOne(targetEntity="App\Entity\Company")
     * @JoinColumn(nullable=false)
     */
    public $company;

    /**
     * @var \DateTime
     *
     * @Column(name="date", type="date")
     */
    public $date;

    /**
     * @var string
     *
     * @Column(name="type", type="string", length=255)
     */
    public $type;

    /** @Column(type="float") **/
    public $surface;

    /** @Column(type="string", length=255, nullable=true) **/
    public $name;

    /** @Column(type="text", nullable=true) **/
    public $comment;

    /**
    * @OneToMany(targetEntity="App\Entity\InterventionParcelle", mappedBy="intervention",cascade={"persist"})
    */
    public $parcelles;

    /**
    * @OneToMany(targetEntity="App\Entity\InterventionProduit", mappedBy="intervention",cascade={"persist"})
    */
    public $produits;

    /**
    * @OneToMany(targetEntity="App\Entity\InterventionMateriel", mappedBy="intervention",cascade={"persist"})
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
