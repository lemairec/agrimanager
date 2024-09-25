<?php

namespace App\Entity\Irrigation;

use App\Repository\Irrigation\TuyauxRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TuyauxRepository::class)]
class Tuyaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column(length: 255)]
    public ?string $params = null;

    #[ORM\Column]
    public array $points = [];

    #[ORM\Column]
    public ?float $longueur = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(nullable: true)]
    public $projet;

    #[ORM\ManyToOne(targetEntity: Borne::class)]
    #[ORM\JoinColumn(nullable: true)]
    public $borneA;

    #[ORM\ManyToOne(targetEntity: Borne::class)]
    #[ORM\JoinColumn(nullable: true)]
    public $borneB;

    public array $altitudes = [];

    public array $points_xy = [];

    public function calculPression($init_pression){
        if($params = "d_100"){
            return $init_pression-0.003*$this->longueur;
        }
        if($params = "d_200"){
            return $init_pression-0.001*$this->longueur;
        }
        return 0;
    }
}
