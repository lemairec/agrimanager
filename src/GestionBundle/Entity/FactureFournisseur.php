<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactureFournisseur
 *
 * @ORM\Table(name="facture_fournisseur")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\FactureFournisseurRepository")
 */
class FactureFournisseur
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @var float
     *
     * @ORM\Column(name="montantHT", type="float")
     */
    public $montantHT;

    /**
     * @var float
     *
     * @ORM\Column(name="montantTTC", type="float")
     */
    public $montantTTC;

    /**
     * @ORM\ManyToOne(targetEntity="GestionBundle\Entity\Compte")
     * @ORM\JoinColumn(nullable=false)
     */
    public $compte;

    /**
     * @ORM\ManyToOne(targetEntity="GestionBundle\Entity\Compte")
     * @ORM\JoinColumn(nullable=false)
     */
    public $banque;

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }

}
