<?php

namespace App\Entity\Cotation;

use App\Repository\Cotation\CotationProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CotationProduitRepository::class)]
class CotationProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column(length: 255)]
    public ?string $categorie = null;

    #[ORM\Column]
    public ?int $home = null;

    #[ORM\Column(length: 255)]
    public ?string $label = null;

    #[ORM\Column(length: 255)]
    public ?string $color = null;

    public function __toString ( ){
        return $this->name;
    }
}
