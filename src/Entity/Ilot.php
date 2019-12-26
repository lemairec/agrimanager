<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ilot
 *
 * @ORM\Entity(repositoryClass="App\Repository\IlotRepository")
 * @ORM\Table(name="ilot")
 */
class Ilot
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    /** @ORM\Column(name="number", type="integer", nullable=true) **/
    public $number;

    /** @ORM\Column(name="surface", type="float") **/
    public $surface;

    /** @ORM\Column(name="name", type="string") **/
    public $name;

    /** @ORM\Column(name="comment", type="string", length=255, nullable=true) **/
    public $comment;

    public function __toString ( ){
        return $this->name." - ".number_format($this->surface,2)." ha";
    }
}
