<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Campagne")
     * @ORM\JoinColumn(nullable=true)
     */
    public $campagne;

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

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Please, upload the product brochure as a PDF file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    public $brochure;

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }

    function getCampagneStr(){
        if($this->campagne){
            return $this->campagne->name;
        } else {
            return "";
        }
    }

}
