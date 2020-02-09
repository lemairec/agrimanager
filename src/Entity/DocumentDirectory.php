<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentDirectoryRepository")
 */
class DocumentDirectory
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
     * @var int
     *
     * @ORM\Column(name="order", type="integer", nullable=true)
     */
    public $order;



    public function __toString ( ){
        return $this->name;
    }
}
