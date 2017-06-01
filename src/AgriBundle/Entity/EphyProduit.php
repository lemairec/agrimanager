<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyProduit
 *
 * @ORM\Table(name="ephy_produit")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\EphyProduitRepository")
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
    * @ORM\OneToMany(targetEntity="AgriBundle\Entity\EphySubstanceProduit", mappedBy="ephyproduit",cascade={"persist"})
    */
    public $substances;


    public function __toString ( ){
        return $this->completeName;
    }

}
