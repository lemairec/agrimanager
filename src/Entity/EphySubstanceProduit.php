<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphySubstanceProduit
 *
 * @ORM\Entity(repositoryClass="App\Repository\EphySubstanceProduitRepository")
 * @ORM\Table(name="ephy_substance_produit")
 */
class EphySubstanceProduit
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
     * @ORM\ManyToOne(targetEntity="App\Entity\EphyProduit", inversedBy="substances")
     * @ORM\JoinColumn(name="ephyproduit", referencedColumnName="amm", nullable=false)
     */
    public $ephyproduit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EphySubstance")
     * @ORM\JoinColumn(name="ephy_substance", referencedColumnName="name")
     */
    public $ephysubstance;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    public $quantity;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
     public $unity;

}
