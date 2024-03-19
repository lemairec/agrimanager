<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'parcelle')]
#[ORM\Entity(repositoryClass: 'App\Repository\ParcelleRepository')]
class Parcelle
{
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Ilot')]
    #[ORM\JoinColumn(nullable: true)]
    public $ilot;

    #[ORM\Column(type: 'integer')]
    public $active;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Campagne')]
    #[ORM\JoinColumn(nullable: false)]
    public $campagne;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    public $pacage;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Culture')]
    #[ORM\JoinColumn(nullable: true)]
    public $culture;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    public $precedent;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    public $couvert;


    #[ORM\Column(type: 'integer', nullable: true)]
    public $ordre;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    public $commune;

    #[ORM\Column(type: 'float', nullable: true)]
    public $surface;

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public $name;

    #[ORM\Column(name: 'complete_name', type: 'string')]
    public $completeName;

    #[ORM\Column(name: 'comment', type: 'string', length: 2048, nullable: true)]
    public $comment;

    #[ORM\OneToMany(targetEntity: 'App\Entity\Variete', mappedBy: 'parcelle', cascade: ['persist'])]
    #[ORM\OrderBy(['ordre' => 'ASC'])]
    public $varietes;

    public $ppfObjRendement;
    public $ppfAzoteUnite;
    public $ppfBesoinCulture;
    public $ppfMiseEnReserve;
    public $ppfBesoinTotal;

    public $greleRdt;
    public $grelePrix;
    public $greleCapital;

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

    #[ORM\Column(type: 'text', name: 'geoJson')]
    public $geoJson;
}


?>
