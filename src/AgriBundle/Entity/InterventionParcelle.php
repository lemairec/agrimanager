<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterventionParcelle
 *
 * @ORM\Table(name="intervention_parcelle")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\InterventionParcelleRepository")
 */
class InterventionParcelle
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
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Intervention", inversedBy="parcelles")
     * @ORM\JoinColumn(nullable=false)
     */
    public $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Parcelle")
     * @ORM\JoinColumn(nullable=false)
     */
    public $parcelle;
}

