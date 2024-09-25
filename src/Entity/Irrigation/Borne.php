<?php

namespace App\Entity\Irrigation;

use App\Repository\Irrigation\BorneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorneRepository::class)]
class Borne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $name = null;

    #[ORM\Column]
    public ?float $lat = null;

    #[ORM\Column]
    public ?float $lon = null;

    #[ORM\Column]
    public ?float $m_x = null;

    #[ORM\Column]
    public ?float $m_y = null;

    #[ORM\Column(nullable: true)]
    public ?float $pression = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(nullable: true)]
    public $projet;

    #[ORM\Column(nullable: true)]
    public ?float $calculate_pression = null;


}
