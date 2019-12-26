<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Campagne
 *
 * @ORM\Entity(repositoryClass="App\Repository\CampagneRepository")
 * @ORM\Table(name="campagne")
 */
class Campagne
{
    /**
     * @var guid
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $id;


    /** @ORM\Column(type="string", length=255) **/
    public $name;

    /** @ORM\Column(type="string", length=255, nullable=true) **/
    public $commercialisation;

    /** @ORM\Column(name="color", type="string", length=25, nullable=true) **/
    public $color;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    public function __toString ( ){
        return $this->name;
    }

}
