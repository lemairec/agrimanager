<?php

namespace App\Entity\Ruche;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Ruche\EssaimRepository")
 * @ORM\Table(name="r_essaim")
 */
class Essaim
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
    public $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $actif;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    public $dateBegin;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ruche\Essaim")
     * @ORM\JoinColumn(nullable=true)
     */
    public $parent;

    public $ruche;
    

    public function __toString(){
        return $this->name;
    }
}
