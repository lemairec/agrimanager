<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterventionProduit
 *
 * @ORM\Entity(repositoryClass="App\Repository\InterventionProduitRepository")
 * @ORM\Table(name="intervention_produit")
 */
class InterventionProduit
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention", inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    public $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit")
     * @ORM\JoinColumn(name="produit_id", referencedColumnName="id", nullable=false)
     */
    public $produit;

    /** @ORM\Column(name="name", type="string", length=255) **/
    public $name;

    /** @ORM\Column(name="qty", type="float") **/ //todo
    public $quantity;

    function getPriceHa(){
        $price = $this->produit->price;
        return  $this->getQuantityHa()*$price;
    }

    function getQuantityHa(){
        $surface = $this->intervention->surface;
        if($surface == 0){
            return 0;
        } else {
            return  $this->quantity/$surface;
        }
    }
}
