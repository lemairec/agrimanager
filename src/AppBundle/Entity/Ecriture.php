<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ecriture
 *
 * @ORM\Table(name="ecriture")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EcritureRepository")
 */
class Ecriture
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
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    public $value;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Compte", inversedBy="ecritures")
     * @ORM\JoinColumn(nullable=false)
     */
    public $compte;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campagne")
     * @ORM\JoinColumn(nullable=true)
     */
    public $campagne;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operation", inversedBy="ecritures")
     * @ORM\JoinColumn(nullable=false)
     */
    public $operation;

}
