<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterventionProduit
 *
 * @ORM\Table(name="intervention_produit")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\InterventionProduitRepository")
 */
class InterventionProduit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Intervention", inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    public $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Produit")
     * @ORM\JoinColumn(name="produit_no_ephy", referencedColumnName="no_ephy")
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

    function getqtyha(){
        $surface = $this->intervention->surface;
        if($surface == 0){
            return 0;
        } else {
            return  round ($this->qty/$surface, 2);
        }
    }
}

