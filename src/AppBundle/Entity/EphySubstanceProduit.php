<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphySubstanceProduit
 *
 * @ORM\Table(name="ephy_substance_produit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EphySubstanceProduitRepository")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EphyProduit", inversedBy="substances")
     * @ORM\JoinColumn(name="ephyproduit", referencedColumnName="complete_name")
     */
    public $ephyproduit;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EphySubstance")
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
