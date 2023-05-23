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

    #[ORM\Column]
    public ?int $work = null;

}
