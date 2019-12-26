<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cours
 *
 * @ORM\Entity(repositoryClass="App\Repository\CoursRepository")
 * @ORM\Table(name="cours")
 */
class Cours
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
     * @ORM\Column(name="produit", type="string", length=255)
     */
    public $produit;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    public $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }
}
