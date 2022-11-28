<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyUsage
 */
#[ORM\Table(name: 'ephy_usage')]
#[ORM\Entity(repositoryClass: 'App\Repository\EphyUsageRepository')]
class EphyUsage
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\EphyProduit', inversedBy: 'usages')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'amm')]
    public $ephyProduit;

    /**
     * @var string
     */
    #[ORM\Column(name: 'identifiant', type: 'string', length: 255)]
    public $identifiantUsage;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    public $stadeCulturalMin;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    public $stadeCulturalMax;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    public $etatUsage;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    public $doseRetenu;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 55)]
    public $doseRetenuUnity;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    public $dar;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    public $nombreAppliMax;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    public $conditionEmploi;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    public $zntAquatique;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    public $zntPlanteNonCibles;
}
