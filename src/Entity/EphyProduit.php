<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyProduit
 *
 * @ORM\Table(name="ephy_produit")
 * @ORM\Entity(repositoryClass="App\Repository\EphyProduitRepository")
 */
class EphyProduit
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="amm", type="integer")
     */
    public $amm;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var string
     * @ORM\Column(name="enable", type="string", length=255)
     */
    public $enable;

    /**
     * @var string
     * @ORM\Column(name="unity", type="string", length=255)
     */
    public $unity = "unite";

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $society;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $typeCommercial;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $gammeUsage;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\EphySubstanceProduit", mappedBy="ephyproduit",cascade={"persist"})
    */
    public $substances;

    /**
    * @ORM\ManyToMany(targetEntity="App\Entity\EphyPhraseRisque")
    * @ORM\JoinTable(
     *  name="ephy_phrase_risques",
     *  joinColumns={
     *      @ORM\JoinColumn(name="produit", referencedColumnName="amm")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="phrase", referencedColumnName="id")
     *  }
     * )
    */
    public $phraseRisques;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\EphyCommercialName", mappedBy="ephyproduit",cascade={"persist"})
    */
    public $commercialeNames;


    public function __toString ( ){
        return $this->amm." - ".$this->name;
    }

    public function addPhraseRisque($phrase){
        foreach ($this->phraseRisques as $p) {
            if($p == $phrase){
                return;
            }
        }
        $this->phraseRisques[] = $phrase;
    }

    public function isCMR(){
        foreach ($this->phraseRisques as $p) {
            if($p->cmr){
                return true;
            }
        }
        return false;
    }

    public function getColor(){
        if($this->isCMR()){
            return "#ffdddd";
        } else {
            return "";
        }
    }

    public function getCompleteName(){
        return $this->amm." - ".$this->name;
    }
}
