<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Ilot
 */
#[ORM\Table(name: 'ilot')]
#[ORM\Entity(repositoryClass: 'App\Repository\IlotRepository')]
class Ilot
{
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Company')]
    #[ORM\JoinColumn(nullable: false)]
    public $company;

    #[ORM\Column(name: 'number', type: 'integer', nullable: true)]
    public $number;

    #[ORM\Column(name: 'surface', type: 'float')]
    public $surface;

    #[ORM\Column(name: 'name', type: 'string')]
    public $name;

    #[ORM\Column(type: 'string')]
    public $typeSol;

    #[ORM\Column(name: 'comment', type: 'string', length: 255, nullable: true)]
    public $comment;

    public function __toString ( ){
        return $this->name." - ".number_format($this->surface,2)." ha";
    }
}
