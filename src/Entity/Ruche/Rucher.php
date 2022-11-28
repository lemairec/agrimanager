<?php

namespace App\Entity\Ruche;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'r_rucher')]
#[ORM\Entity(repositoryClass: 'App\Repository\Ruche\RucherRepository')]
class Rucher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 255)]
    public $lieu;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $description;

    #[ORM\OneToMany(targetEntity: 'App\Entity\Ruche\Ruche', mappedBy: 'rucher')]
    public $ruches;

    public function __toString(){
        return $this->lieu;
    }

    public function ruchesCount(){
        return count($this->ruches);
    }
}
