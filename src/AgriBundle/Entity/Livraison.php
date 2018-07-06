<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Livraison
 *
 * @ORM\Table(name="livraison")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\LivraisonRepository")
 */
class Livraison
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
     * @ORM\Column(name="date", type="datetime")
     */
    public $date;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable=true))
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicule", type="string", length=255,nullable=true))
     */
    public $vehicule;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255,nullable=true))
     */
    public $espece;

    /**
     * @var float
     *
     * @ORM\Column(type="float",nullable=true)
     */
    public $poid_total;

    /**
     * @var float
     *
     * @ORM\Column(type="float",nullable=true)
     */
    public $tare;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $humidite;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $impurete;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $ps;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $proteine;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $calibrage;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $poid_norme;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Parcelle")
     * @ORM\JoinColumn(name="parcelle_id",nullable=true)
     */
    public $parcelle;

    function getDateStr(){
        return $this->date->format('d/m/y');
    }
}
