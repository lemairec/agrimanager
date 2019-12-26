<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyProduit
 *
 * @ORM\Entity(repositoryClass="App\Repository\EphyProduitRepository")
 * @ORM\Table(name="ephy_produit")
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
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $fonctions;

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

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\EphyUsage", mappedBy="ephyProduit")
    */
    public $usages;

    public function isCategory1(){
        $cat = ["H300","H301", "H310", "H311", "H340", "H350", "H350i", "H360", "H370", "H372"];
        foreach($this->phraseRisques as $phraseRisque){
            if(in_array($phraseRisque->id, $cat)){
                return true;
            }
        }
        return false;
    }

    public function isCategory2(){
        $cat = ["H341", "H351", "H370"];
        foreach($this->phraseRisques as $phraseRisque){
            if(in_array($phraseRisque->id, $cat)){
                return true;
            }
        }
        return false;
    }

    public function isCategory3(){
        $cat = ["H373"];
        foreach($this->phraseRisques as $phraseRisque){
            if(in_array($phraseRisque->id, $cat)){
                return true;
            }
        }
        return false;
    }

    public function isCategory4(){
        $cat = ["H361", "H362"];
        foreach($this->phraseRisques as $phraseRisque){
            if(in_array($phraseRisque->id, $cat)){
                return true;
            }
        }
        return false;
    }


    public function isT(){
        foreach($this->phraseRisques as $phraseRisque){
            if($phraseRisque->id == "H300" || $phraseRisque->id == "H301" || $phraseRisque->id == "H310"
                || $phraseRisque->id == "H311" || $phraseRisque->id == "H330" || $phraseRisque->id == "H331"
                || $phraseRisque->id == "H340" || $phraseRisque->id == "H350" || $phraseRisque->id == "H350i"
                || $phraseRisque->id == "H360" || $phraseRisque->id == "H370" || $phraseRisque->id == "H372"){
                    return true;

            }
        }
        return false;
    }


    public function getUrlAnses ( ){
        $res = str_replace(" ", "-", $this->name);
        $res = strtolower($res);
        return "//ephy.anses.fr/ppp/".$res;
    }

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

    public function getAmm(){
        return $this->amm;
    }
}
