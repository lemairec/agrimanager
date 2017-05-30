<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyProduit
 *
 * @ORM\Table(name="produit_ephy")
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
     * @ORM\Column(name="substances", type="string", length=255)
     */
    public $substances;

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

    public function __toString ( ){
        return $this->completeName;
    }

}
