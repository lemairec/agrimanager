<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Culture
 *
 * @ORM\Entity(repositoryClass="App\Repository\CultureRepository")
 * @ORM\Table(name="culture")
 */
class Culture
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


    /** @ORM\Column(name="name", type="string", length=255) **/
    public $name;

    /** @ORM\Column(type="string", length=45, nullable=true) **/
    public $codetelepac;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MetaCulture")
     * @ORM\JoinColumn(nullable=true)
     */
    public $metaCulture;

    /** @ORM\Column(name="color", type="string", length=25, nullable=true) **/
    public $color;

    /** @ORM\Column(type="string", length=255, nullable=true) **/
    public $commercialisation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    /** @ORM\Column(type="float", nullable = true) **/
    public $rendementObj;

    /** @ORM\Column(type="float", nullable = true) **/
    public $prixObj;

    public function getRendementPrev(){
        if($this->metaCulture){
            return $this->rendementObj;
        }
        return 0;
    }

    public function __toString ( ){
        return $this->name;
    }
}
