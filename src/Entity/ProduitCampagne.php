<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProduitCampagne
 */
#[ORM\Table(name: 'produit_campagne')]
#[ORM\Entity(repositoryClass: 'App\Repository\ProduitCampagneRepository')]
class ProduitCampagne
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public $id;

    /**
     * @var float
     */
    #[ORM\Column(name: 'qty_totale', type: 'float')]
    public $qty_totale = 0;

    /**
     * @var float
     */
    #[ORM\Column(name: 'stock', type: 'float')]
    public $stock = 0;

    /**
     * @var float
     */
    #[ORM\Column(name: 'qty', type: 'float')]
    public $price = 0;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Campagne')]
    #[ORM\JoinColumn(nullable: false)]
    public $campagne;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Produit')]
    #[ORM\JoinColumn(nullable: false)]
    public $produit;
}
