<?php

namespace App\Entity\Silo;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Silo\BaliseRepository")
 */
class Balise
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;


    /** @ORM\Column(type="string", length=255) **/
    public $label = "";


    /** @ORM\Column(type="datetime", nullable=true) **/
    public $last_update;

    /** @ORM\Column(type="float", nullable=true) **/
    public $last_temp;
}
