<?php

namespace App\Entity\Agrigps;

use App\Repository\Agrigps\BaliseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'gps_balise')]
#[ORM\Entity(repositoryClass: BaliseRepository::class)]
class Balise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public $myId;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Company')]
    #[ORM\JoinColumn(nullable: true)]
    public $company;
        
    #[ORM\Column(type: 'float')]
    public $latitude;

    #[ORM\Column(type: 'float')]
    public $longitude;

    #[ORM\Column(type: 'datetime')]
    public $my_datetime;

    #[ORM\Column(type: 'string', length: 255)]
    public $name = "";

    #[ORM\Column(type: 'string', length: 255)]
    public $color = "";

    #[ORM\Column(type: 'boolean')]
    public $enable = true;
}
