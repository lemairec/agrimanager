<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyUsage
 *
 * @ORM\Table(name="ephy_usage")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EphyUsageRepository")
 */
class EphyUsage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EphyProduit")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="complete_name")
     */
    public $ephyProduit;

    /**
     * @var string
     * @ORM\Column(name="identifiant", type="string", length=255)
     */
    public $identifiantUsage;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    public $stadeCulturalMin;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    public $stadeCulturalMax;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $etatUsage;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    public $doseRetenu;

    /**
     * @var string
     * @ORM\Column(type="string", length=15)
     */
    public $doseRetenuUnity;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    public $dar;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    public $nombreApportMax;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $conditionEmploi;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    public $zntAquatique;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    public $zntPlanteNonCibles;
}
