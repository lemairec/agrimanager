<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Culture
 *
 * @ORM\Entity(repositoryClass="App\Repository\CultureRepository")
 * @ORM\Table(name="culture")
 */
class Culture
{
    /**
     * @var guid
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $id;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MetaCulture")
     * @ORM\JoinColumn(nullable=true)
     */
    public $metaCulture;

    /**
     * @var color
     *
     * @ORM\Column(name="color", type="string", length=25, nullable=true)
     */
    public $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $commercialisation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    /**
     * @ORM\Column(type="float", nullable = true)
     */
    public $rendementObj;

    /**
     * @ORM\Column(type="float", nullable = true)
     */
    public $prixObj;

    public function getRendementPrev(){
        if($this->metaCulture){
            return $this->rendementObj;
        }
        return 0;
    }

    public function __toString ( ){
        return $this->name;
    }
}
