<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Achat
 *
 * @ORM\Entity(repositoryClass="App\Repository\AchatRepository")
 * @ORM\Table(name="achat")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Campagne")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit")
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
    public $qty = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="price_total", type="float")
     */
    public $price_total = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    public $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gestion\FactureFournisseur")
     * @ORM\JoinColumn(nullable=true)
     */
    public $facture;

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }

    function getPrice(){
        if($this->qty != 0){
            return $this->price_total/$this->qty;
        } else {
            return null;
        }
    }
}
