<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecolteRepository")
 * @ORM\Table(name="intervention_recolte")
 */
class InterventionRecolte
{
    /**
     * @ORM\Column(type="integer", name="recolteId")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $recolteId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention", inversedBy="recoltes")
     * @ORM\JoinColumn(name="intervention_id", referencedColumnName="id", nullable=false)
     */
    public $intervention;

     /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProduitRecolte")
     * @ORM\JoinColumn(name="idProduit", referencedColumnName="idProduitRecolte", nullable=true)
     */
    public $produit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Unite")
     * @ORM\JoinColumn(name="Unite", referencedColumnName="idUnite", nullable=true)
     */
    public $Unite;

    /** @ORM\Column(type="integer", name="destinationType") **/
    public $destinationType;
    
    /** @ORM\Column(type="datetime", name="date") **/
    public $date;

    /** @ORM\Column(type="float") **/
    public $volume;

    /** @ORM\Column(type="string", length=255) **/
    public $stockage;

    /** @ORM\Column(type="string", length=255) **/
    public $bl;

    /** @ORM\Column(type="integer", nullable = true) **/
    public $partenaire;
}

?>