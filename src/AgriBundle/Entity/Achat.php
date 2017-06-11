<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Achat
 *
 * @ORM\Table(name="achat")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\AchatRepository")
 */
class Achat
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Produit")
     * @ORM\JoinColumn(nullable=false)
     */
    public $produit;

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
     * @var float
     *
     * @ORM\Column(name="qty", type="float")
     */
    public $qty;

    /**
     * @var float
     *
     * @ORM\Column(name="unity", type="string", length=255)
     */
    public $unity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    public $price;

    /**
     * @var float
     *
     * @ORM\Column(name="price_total", type="float")
     */
    public $price_total;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    public $comment;

    function get_date(){
        return $this->date->format(' d/m/Y');
    }
}
