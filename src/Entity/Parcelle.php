<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parcelle
 *
 * @ORM\Entity(repositoryClass="App\Repository\ParcelleRepository")
 * @ORM\Table(name="parcelle")
 */
class Parcelle
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Campagne")
     * @ORM\JoinColumn(nullable=false)
     */
    public $campagne;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Culture")
     * @ORM\JoinColumn(nullable=true)
     */
    public $culture;

    /** @ORM\Column(name="surface", type="float") **/
    public $surface = 0;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ilot")
     * @ORM\JoinColumn(nullable=true)
     */
    public $ilot;

    /** @ORM\Column(name="name", type="string", nullable=true) **/
    public $name;

    /** @ORM\Column(name="complete_name", type="string") **/
    public $completeName;

    /** @ORM\Column(name="comment", type="string", length=2048, nullable=true) **/
    public $comment;

    /** @ORM\Column(name="active", type="boolean") **/
    public $active = true;

    public function getIlotName(){
        if($this->ilot){
            return $this->ilot->name;
        } else {
            return "";
        }
    }

    public function getCultureColor(){
        if($this->culture){
            return $this->culture->color;
        } else {
            return "";
        }
    }

    public function getCultureName(){
        if($this->culture){
            return $this->culture->name;
        } else {
            return "???";
        }
    }

    public function __toString ( ){
        return $this->completeName;
    }

}
