<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterventionProduit
 *
 * @ORM\Table(name="intervention_produit")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\InterventionProduitRepository")
 */
class InterventionProduit
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
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Produit")
     * @ORM\JoinColumn(name="produit_no_ephy", referencedColumnName="no_ephy")
     */
    public $produit;
    
    /**
     * @var float
     *
     * @ORM\Column(name="qty", type="float")
     */
    public $qty;

}

