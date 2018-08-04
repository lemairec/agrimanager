<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterventionProduit
 *
 * @ORM\Table(name="intervention_produit")
 * @ORM\Entity(repositoryClass="App\Repository\InterventionProduitRepository")
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

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;


    /**
     * @var float
     *
     * @ORM\Column(name="qty", type="float")
     */
    public $qty;

    function getPriceHa(){
        $price = $this->produit->price;
        return  $this->getqtyha()*$price;
    }

    function getQtyHa(){
        $surface = $this->intervention->surface;
        if($surface == 0){
            return 0;
        } else {
            return  $this->qty/$surface;
        }
    }
}
