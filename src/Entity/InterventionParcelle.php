<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InterventionParcelleRepository")
 * @ORM\Table(name="intervention_parcelle")
 */
class InterventionParcelle
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention", inversedBy="parcelles")
     * @ORM\JoinColumn(name="intervention_id", referencedColumnName="id", nullable=false)
     */
    public $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Parcelle")
     * @ORM\JoinColumn(name="parcelle_id",nullable=false)
     */
    public $parcelle;
}
