<?php

namespace App\Entity\Iot;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table]
#[ORM\Index(name: 'datetime_idx', columns: ['datetime'])]
#[ORM\Entity(repositoryClass: 'App\Repository\Iot\MoteurHistRepository')]
class MoteurHist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Iot\Moteur')]
    #[ORM\JoinColumn(nullable: false)]
    public $moteur;

    #[ORM\Column(type: 'datetime')]
    public $datetime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public $rounded_datetime;

    #[ORM\Column(type: 'float')]
    public $temp_ext;

    #[ORM\Column(type: 'float', nullable: true)]
    public $temp_sonde;

    #[ORM\Column(type: 'integer')]
    public $on_off;

    #[ORM\Column(type: 'integer')]
    public $desired_on_off;

    #[ORM\Column(type: 'string', nullable: true)]
    public $debug;
}