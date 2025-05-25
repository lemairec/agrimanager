<?php

namespace App\Entity\Cotation;

use App\Repository\Cotation\PrixMoyenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrixMoyenRepository::class)]
class PrixMoyen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    public $source;

    #[ORM\Column(type: 'string', length: 100)]
    public $campagne;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Cotation\CotationProduit')]
    #[ORM\JoinColumn(nullable: true)]
    public $produit;

    #[ORM\Column(type: 'float', nullable: true)]
    public $accompte_price;

    #[ORM\Column(type: 'date', nullable: true)]
    public $c1_date;

    #[ORM\Column(type: 'float', nullable: true)]
    public $c1_price;

    #[ORM\Column(type: 'date', nullable: true)]
    public $c2_date;

    #[ORM\Column(type: 'float', nullable: true)]
    public $c2_price;

    #[ORM\Column(type: 'date', nullable: true)]
    public $c3_date;

    #[ORM\Column(type: 'float', nullable: true)]
    public $c3_price;

    #[ORM\Column(type: 'date', nullable: true)]
    public $c4_date;

    #[ORM\Column(type: 'float', nullable: true)]
    public $c4_price;

    public function getPrixTotal(){
        return $this->accompte_price+$this->c1_price+$this->c2_price+$this->c3_price+$this->c4_price;
    }
    
}
