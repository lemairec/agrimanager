<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InterventionProduitRepository")
 * @ORM\Table(name="intervention_produit")
 */
class InterventionProduit
{
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention", inversedBy="produits")
     * @ORM\JoinColumn(name="intervention_id", referencedColumnName="id", nullable=false)
     */
    public $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit")
     * @ORM\JoinColumn(name="produit_id", referencedColumnName="id", nullable=false)
     */
    public $produit;

    /** @ORM\Column(name="name", type="string", length=255) **/
    public $name;

    /** @ORM\Column(type="float") **/
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
