<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterventionMateriel
 *
 * @ORM\Table(name="intervention_materiel")
 * @ORM\Entity(repositoryClass="App\Repository\InterventionMaterielRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention", inversedBy="materiels")
     * @ORM\JoinColumn(nullable=false)
     */
    public $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Materiel")
     * @ORM\JoinColumn(name="materiel_id",nullable=false)
     */
    public $materiel;
}
