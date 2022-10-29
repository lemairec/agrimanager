<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VarieteRepository")
 */
class Variete
{
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;


    /**
     * @ORM\Column(type="integer")
     */
    public $ordre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Culture")
     * @ORM\JoinColumn(nullable=true)
     */
    public $precedent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Parcelle", inversedBy="varietes")
     * @ORM\JoinColumn(nullable=false)
     */
    public $parcelle;

    /** @ORM\Column(name="comment", type="string", length=2048, nullable=true) **/
    public $comment;


    /** @ORM\Column(type="float", nullable = true) **/
    public $surface;
}
