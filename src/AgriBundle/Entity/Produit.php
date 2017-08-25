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
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Campagne")
     * @ORM\JoinColumn(nullable=false)
     */
    public $campagne;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    /**
     * @var string
     *
     * @ORM\Column(name="complete_name", type="string", length=255)
     */
    public $completeName;

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
     * @var string
     *
     * @ORM\Column(name="unity", type="string", length=255)
     */
    public $unity = "unité";

    /**
     * @var float
     *
     * @ORM\Column(name="qty", type="float")
     */
    public $qty = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    public $price = 0;

    /**
     * @ORM\ManyToOne(targetEntity="EphyBundle\Entity\EphyProduit")
     * @ORM\JoinColumn(name="ephy_produit", referencedColumnName="complete_name")
     */
    public $ephyProduit;

    /**
     * @var float
     *
     * @ORM\Column(name="n", type="float")
     */
    public $n = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="p", type="float")
     */
    public $p = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="k", type="float")
     */
    public $k = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="mg", type="float")
     */
    public $mg = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="s", type="float")
     */
    public $s = 0;
}
