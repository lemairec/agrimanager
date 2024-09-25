<?php

namespace App\Entity\Iot;

use App\Repository\Iot\DataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataRepository::class)]
class Data
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Iot\Iot')]
    #[ORM\JoinColumn(nullable: false)]
    public $iot;

    #[ORM\Column(type: 'datetime')]
    public $datetime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public $rounded_datetime;

    #[ORM\Column(type: 'float', nullable: true)]
    public $data1;

    #[ORM\Column(type: 'float', nullable: true)]
    public $data2;
    
    #[ORM\Column(type: 'float', nullable: true)]
    public $data3;

    #[ORM\Column(type: 'float', nullable: true)]
    public $data4;

    #[ORM\Column(type: 'float', nullable: true)]
    public $data5;

    #[ORM\Column(type: 'float', nullable: true)]
    public $data6;

    #[ORM\Column(type: 'float', nullable: true)]
    public $data7;

    #[ORM\Column(type: 'float', nullable: true)]
    public $data8;
}
