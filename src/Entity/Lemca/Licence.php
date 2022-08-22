<?php

namespace App\Entity\Lemca;

use App\Repository\Lemca\LicenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LicenceRepository::class)
 */
class Licence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $licence;

    /**
     * @ORM\Column(type="date")
     */
    public $date_create;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $panel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $boitier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $licence_decode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $short_desc;

    /**
     * @ORM\Column(type="text", nullable = true)
     */
    public $description;
}
