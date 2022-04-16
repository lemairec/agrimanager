<?php

namespace App\Entity\Agrigps;

use App\Repository\Agrigps\BaliseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BaliseRepository::class)
 * @ORM\Table(name="my_balise")
 */
class Balise
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255) **/
    public $my_id = "";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=true)
     */
    public $company;
        
    /**
     * @ORM\Column(type="float")
     */
    public $latitude;

    /**
     * @ORM\Column(type="float")
     */
    public $longitude;

    /** @ORM\Column(type="string", length=255) **/
    public $name = "";

    /** @ORM\Column(type="string", length=255) **/
    public $color = "";
}
