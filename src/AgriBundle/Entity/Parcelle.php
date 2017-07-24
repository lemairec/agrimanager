<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parcelle
 *
 * @ORM\Table(name="parcelle")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\ParcelleRepository")
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
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Campagne")
     * @ORM\JoinColumn(nullable=false)
     */
    public $campagne;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Culture")
     * @ORM\JoinColumn(nullable=true)
     */
    public $culture;

    /**
     * @var float
     *
     * @ORM\Column(name="surface", type="float")
     */
    public $surface = 0;


    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Ilot")
     * @ORM\JoinColumn(nullable=true)
     */
    public $ilot;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="complete_name", type="string")
     */
    public $completeName;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $rendement = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=2048, nullable=true)
     */
    public $comment;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    public $active = true;

    public function getIlotName(){
        if($this->ilot){
            return $this->ilot->name;
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
