<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MetaCultureRepository")
 */
class MetaCulture
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
     * @var string
     *
     * @ORM\Column(name="cutureUsage", type="string", length=255, nullable=true)
     */
    public $cultureUsage;

    public function __toString ( ){
        return $this->name;
    }
}
