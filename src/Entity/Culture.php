<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Culture
 *
 * @ORM\Table(name="culture")
 * @ORM\Entity(repositoryClass="App\Repository\CultureRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    public function getRendementPrev(){
        if($this->metaCulture){
            return $this->metaCulture->rendement_prev;
        }
        return 0;
    }

    public function __toString ( ){
        return $this->name;
    }
}
