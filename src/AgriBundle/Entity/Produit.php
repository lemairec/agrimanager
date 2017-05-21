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
    public $unity;

    /**
     * @var float
     *
     * @ORM\Column(name="qty", type="float")
     */
    public $qty;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    public $price;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\EphyProduit")
     * @ORM\JoinColumn(name="ephy_produit", referencedColumnName="complete_name")
     */
    public $ephyProduit;

    /**
     * @var float
     *
     * @ORM\Column(name="n", type="float")
     */
    public $n;

    /**
     * @var float
     *
     * @ORM\Column(name="p", type="float")
     */
    public $p;
    /**
     * @var float
     *
     * @ORM\Column(name="k", type="float")
     */
    public $k;
    /**
     * @var float
     *
     * @ORM\Column(name="mg", type="float")
     */
    public $mg;
    /**
     * @var float
     *
     * @ORM\Column(name="s", type="float")
     */
    public $s;
}
