<?php

namespace App\Entity\Ruche;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Ruche\RucheRepository")
 * @ORM\Table(name="r_ruche")
 */
class Ruche
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $color;

    /**
     * @ORM\Column(type="integer")
     */
    public $nbrCadres;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ruche\Rucher")
     * @ORM\JoinColumn(nullable=true)
     */
    public $rucher;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Ruche\Essaim")
     * @ORM\JoinColumn(nullable=true)
     */
    public $essaim;

    public function __toString(){
        return $this->name." ".$this->color." ".$this->nbrCadres;
    }
}
