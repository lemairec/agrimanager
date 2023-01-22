<?php

namespace App\Entity\Lemca;

use App\Repository\Lemca\BranchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BranchRepository::class)]
class Branch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $filename;

    #[ORM\Column(type: 'date')]
    public $date;

    #[ORM\Column(type: 'text')]
    public $log;
}
