<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ecriture
 *
 * @ORM\Entity(repositoryClass="App\Repository\EcritureRepository")
 * @ORM\Table(name="ecriture")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="ecritures")
     * @ORM\JoinColumn(nullable=false)
     */
    public $compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campagne")
     * @ORM\JoinColumn(nullable=true)
     */
    public $campagne;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operation", inversedBy="ecritures")
     * @ORM\JoinColumn(nullable=false)
     */
    public $operation;

}
