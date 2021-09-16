<?php

namespace App\Entity;

use App\Repository\GpsParcelleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GpsParcelleRepository::class)
 */
class GpsParcelle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="datetime")
     */
    public $dateUpdate;

    /**
     * @ORM\Column(type="float")
     */
    public $surface;

    /**
     * @ORM\Column(type="text")
     */
    public $parcelle = "";
    
    /**
     * @var string
     *
     * @ORM\Column(name="user_email", type="string", length=255,nullable=true)
     */
    public $userEmail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    public $user;

    /**
     * @ORM\Column(type="boolean")
     */
    public $deleted = false;
}
