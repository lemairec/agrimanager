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
     * @var int
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
     * @ORM\Id
     * @ORM\Column(name="complete_name", type="string", length=255)
     */
    public $completeName;


}