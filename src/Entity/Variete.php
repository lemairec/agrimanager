<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VarieteRepository")
 */
class Variete
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Culture")
     * @ORM\JoinColumn(nullable=true)
     */
    public $precedent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $variete;

    /** @ORM\Column(type="float", nullable = true) **/
    public $surface;
}
