<?php

namespace App\Entity\Iot;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

#[ORM\Table]
#[ORM\Index(name: 'datetime_idx', columns: ['datetime'])]
#[ORM\Entity(repositoryClass: 'App\Repository\Iot\TemperatureRepository')]
class Temperature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Iot\Balise')]
    #[ORM\JoinColumn(nullable: false)]
    public $balise;

    #[ORM\Column(type: 'datetime')]
    public $datetime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public $rounded_datetime;

    #[ORM\Column(type: 'float')]
    public $temp;
}
