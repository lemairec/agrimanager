<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterventionMateriel
 *
 * @ORM\Table(name="intervention_materiel")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterventionMaterielRepository")
 */
class InterventionMateriel
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Intervention", inversedBy="materiels")
     * @ORM\JoinColumn(nullable=false)
     */
    public $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Materiel")
     * @ORM\JoinColumn(name="materiel_id",nullable=false)
     */
    public $materiel;
}
