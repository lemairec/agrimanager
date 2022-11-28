<?php

namespace App\Entity\Ruche;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'r_ruche')]
#[ORM\Entity(repositoryClass: 'App\Repository\Ruche\RucheRepository')]
class Ruche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 255)]
    public $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $color;

    #[ORM\Column(type: 'integer')]
    public $nbrCadres;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $description;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Ruche\Rucher')]
    #[ORM\JoinColumn(nullable: true)]
    public $rucher;

    #[ORM\OneToOne(targetEntity: 'App\Entity\Ruche\Essaim')]
    #[ORM\JoinColumn(nullable: true)]
    public $essaim;

    public function __toString(){
        $s = $this->rucher." - ".$this->name." ".$this->color." ".$this->nbrCadres." ";
        if($this->essaim){
            $s = $s." - ".$this->essaim;
        }
        return $s;
    }
}
