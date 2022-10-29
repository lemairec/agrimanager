<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CampagneRepository")
 * @ORM\Table(name="campagne")
 */
class Campagne
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    /** @ORM\Column(type="string", length=255) **/
    public $name;

    /** @ORM\Column(type="string", length=255, nullable=true) **/
    public $commercialisation;

    /** @ORM\Column(name="color", type="string", length=25, nullable=true) **/
    public $color;

    /** @ORM\Column(type="integer", nullable = true, name="anneeStart") **/
    public $anneeStart;

    public function __toString ( ){
        return $this->name;
    }

}
