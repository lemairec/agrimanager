<?php

namespace App\Entity\Iot;

use App\Repository\Iot\SechoirRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SechoirRepository::class)]
class Sechoir
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    public $datetime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public $rounded_datetime;

    #[ORM\Column(type: 'float', nullable: true)]
    public $t_ext;

    #[ORM\Column(type: 'float', nullable: true)]
    public $t_cold;
    
    #[ORM\Column(type: 'float', nullable: true)]
    public $t_hot;

    #[ORM\Column(type: 'float', nullable: true)]
    public $t_out;

    #[ORM\Column(type: 'float', nullable: true)]
    public $t_cons;

    #[ORM\Column(type: 'float', nullable: true)]
    public $nb_cycle;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $bruleur;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $m_cold;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $m_hot;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $description;

    #[ORM\Column(type: 'json', nullable: true)]
    public $my_data;
}

