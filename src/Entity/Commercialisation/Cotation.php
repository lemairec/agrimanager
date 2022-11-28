<?php

namespace App\Entity\Commercialisation;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\Commercialisation\CotationRepository')]
class Cotation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 100)]
    public $source;

    #[ORM\Column(type: 'string', length: 100)]
    public $campagne;

    #[ORM\Column(type: 'string', length: 100)]
    public $produit;

    #[ORM\Column(type: 'float')]
    public $value;

    #[ORM\Column(type: 'float', nullable: true)]
    public $valueStockage;

    #[ORM\Column(type: 'float', nullable: true)]
    public $valueStockageEnd;

    #[ORM\Column(name: 'date', type: 'date')]
    public $date;


}
