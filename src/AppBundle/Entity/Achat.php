<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Achat
 *
 * @ORM\Table(name="achat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AchatRepository")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campagne")
     * @ORM\JoinColumn(nullable=false)
     */
    public $campagne;

    /**
     * @var string
     *
     * @ORM\Column(name="extern_id", type="string", length=255, nullable=true)
     */
    public $externId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Produit")
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
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
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
     * @var float
     *
     * @ORM\Column(name="complement_total", type="float")
     */
    public $complement_total = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="complement", type="float")
     */
    public $complement = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    public $comment;

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }
}
