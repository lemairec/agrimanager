<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyUsage
 *
 * @ORM\Table(name="ephy_usage")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\EphyUsageRepository")
 */
class EphyUsage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string
     * @ORM\Column(name="identifiant_id", type="string", length=255)
     */
    public $identifiantId;

    /**
     * @var string
     * @ORM\Column(name="identifiant", type="string", length=255)
     */
    public $identifiant;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $conditions;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\EphyProduit")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="complete_name")
     */
    public $ephyProduit;

}
