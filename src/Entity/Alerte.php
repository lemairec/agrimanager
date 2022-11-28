<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\AlerteRepository')]
class Alerte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Campagne')]
    #[ORM\JoinColumn(nullable: false)]
    public $campagne;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Intervention')]
    #[ORM\JoinColumn(nullable: true)]
    public $intervention;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\InterventionParcelle')]
    #[ORM\JoinColumn(nullable: true)]
    public $interventionParcelle;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\InterventionProduit')]
    #[ORM\JoinColumn(nullable: true)]
    public $interventionProduit;

    /**
     * @var string
     */
    #[ORM\Column(name: 'type', type: 'string', length: 255)]
    public $type;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    public $description;

    public function getIntervention(){
        return $this->interventionParcelle->intervention;
    }

    public function getDescription(){
        if($this->type == "NOT_FOUND"){
            return $this->type;
        }
        return $this->type;

    }
}
