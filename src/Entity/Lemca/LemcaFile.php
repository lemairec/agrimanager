<?php

namespace App\Entity\Lemca;

use App\Repository\Lemca\LemcaFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LemcaFileRepository::class)]
class LemcaFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $filename;

    #[ORM\Column(type: 'datetime')]
    public $datetime;
}
