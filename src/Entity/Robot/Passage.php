<?php

namespace App\Entity\Robot;

use App\Repository\Robot\PassageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PassageRepository::class)]
class Passage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $datetime = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public ?Robot $robot = null;

    #[ORM\Column]
    public ?float $latitude = null;

    #[ORM\Column]
    public ?float $longitude = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $l1 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $l2 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $l3 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $l4 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $l5 = null;

    #[ORM\Column(type: 'text',nullable: true)]
    public $log = null;

    #[ORM\Column(type: 'text',nullable: true)]
    public $log1 = null;

    #[ORM\Column]
    public ?int $work = null;

}
