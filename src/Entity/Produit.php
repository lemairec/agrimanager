<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 * @ORM\Table(name="produit")
 */
class Produit
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

    /** @ORM\Column(name="complete_name", type="string", length=255) **/
    public $completeName;

    /** @ORM\Column(name="name", type="string", length=255) **/
    public $name;

    /** @ORM\Column(name="type", type="string", length=255) **/
    public $type;

    /** @ORM\Column(name="unity", type="string", length=255) **/
    public $unity = "unité";

    /** @ORM\Column(name="qty", type="float") **/
    public $qty = 0;

    /** @ORM\Column(name="price", type="float") **/
    public $price = 0;

    /** Phyto **/
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EphyProduit")
     * @ORM\JoinColumn(name="ephy_produit", referencedColumnName="amm")
     */
    public $ephyProduit;

    /** ENGRAIS **/
    
    /** @ORM\Column(name="n", type="float") **/
    public $engrais_n = 0;

    /** @ORM\Column(name="p", type="float") **/
    public $engrais_p = 0;

    /** @ORM\Column(name="k", type="float") **/
    public $engrais_k = 0;
    
    /** @ORM\Column(name="mg", type="float") **/
    public $engrais_mg = 0;

    /** @ORM\Column(name="s", type="float") **/
    public $engrais_so3 = 0;

    public function __toString ( ){
        $res = $this->name;
        $res = $res." (".$this->unity.")";
        return $res;
    }

    public function isCMR(){
        if($this->ephyProduit){
            return $this->ephyProduit->isCMR();
        }
        return false;
    }


    public function getColor(){
        if($this->ephyProduit){
            return $this->ephyProduit->getColor();
        } else {
            return "";
        }
    }
    /** @ORM\Column(type="string") **/
    public $migration = "";

    public function getAmm(){
        if($this->ephyProduit){
            return $this->ephyProduit->amm;
        } else {
            return "";
        }
    }
}
