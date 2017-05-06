<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\ProduitRepository")
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    public $type;
    
    /**
    /**
     * @var float
     *
     * @ORM\Column(name="qty", type="float")
     */
    public $qty;
    

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\ProduitEphy")
     * @ORM\JoinColumn(name="produit_no_ephy", referencedColumnName="no_ephy")
     */
    public $produitEphy;
}

