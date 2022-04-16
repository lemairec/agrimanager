<?php

namespace App\Entity\Agrigps;

use App\Repository\Agrigps\GpsParcelleRepository;
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
    public $datetime;

    /**
     * @ORM\Column(type="float")
     */
    public $surface;

    /**
     * @ORM\Column(type="boolean")
     */
    public $active;

    /** @ORM\Column(type="string", length=255) **/
    public $name = "";
    
    /** @ORM\Column(type="string", length=255) **/
    public $status = "";
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=true)
     */
    public $company;

     /**
     * @ORM\Column(type="json")
     */
    public $data = [];
}
