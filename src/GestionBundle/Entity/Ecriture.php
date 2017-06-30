<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ecriture
 *
 * @ORM\Table(name="ecriture")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\EcritureRepository")
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
     * @ORM\Column(name="price", type="float")
     */
    public $price;

    /**
     * @ORM\ManyToOne(targetEntity="GestionBundle\Entity\Compte")
     * @ORM\JoinColumn(nullable=false)
     */
    public $compte;

    /**
     * @ORM\ManyToOne(targetEntity="GestionBundle\Entity\Operation")
     * @ORM\JoinColumn(nullable=false)
     */
    public $operation;

}
