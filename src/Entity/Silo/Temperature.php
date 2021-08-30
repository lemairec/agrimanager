<?php

namespace App\Entity\Silo;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Silo\TemperatureRepository")
 */
class Temperature
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Silo\Balise")
     * @ORM\JoinColumn(nullable=false)
     */
    public $balise;

    /** @ORM\Column(type="datetime") **/
    public $datetime;

    /** @ORM\Column(type="datetime", nullable=true) **/
    public $rounded_datetime;

    /** @ORM\Column(type="float") **/
    public $temp;
}
