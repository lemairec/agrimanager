<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gasoil
 *
 * @ORM\Table(name="gasoil")
 * @ORM\Entity(repositoryClass="App\Repository\GasoilRepository")
 */
class Gasoil
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    public $type;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=2048, nullable=true)
     */
    public $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campagne")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Materiel")
     * @ORM\JoinColumn(nullable=true)
     */
    public $materiel;

    /**
     * @var int
     *
     * @ORM\Column(name="litre", type="integer")
     */
    public $litre;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    public $litrePompe;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_heure", type="integer",nullable=true)
     */
    public $nb_heure;
}
