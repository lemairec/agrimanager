<?php

namespace EphyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyProduit
 *
 * @ORM\Table(name="ephy_produit")
 * @ORM\Entity(repositoryClass="EphyBundle\Repository\EphyProduitRepository")
 */
class EphyProduit
{
    /**
     * @var string
     * @ORM\Column(name="amm", type="string")
     */
    public $amm;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

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
     * @ORM\Id
     * @ORM\Column(name="complete_name", type="string", length=255)
     */
    public $completeName;

    /**
    * @ORM\OneToMany(targetEntity="EphyBundle\Entity\EphySubstanceProduit", mappedBy="ephyproduit",cascade={"persist"})
    */
    public $substances;

    /**
    * @ORM\OneToMany(targetEntity="EphyBundle\Entity\EphyCommercialName", mappedBy="ephyproduit",cascade={"persist"})
    */
    public $commercialeNames;


    public function __toString ( ){
        return $this->completeName;
    }

}
