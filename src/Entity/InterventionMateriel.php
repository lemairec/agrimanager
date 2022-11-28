<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'intervention_materiel')]
#[ORM\Entity(repositoryClass: 'App\Repository\InterventionMaterielRepository')]
class InterventionMateriel
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

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Intervention', inversedBy: 'materiels')]
    #[ORM\JoinColumn(name: 'intervention_id', referencedColumnName: 'id', nullable: false)]
    public $intervention;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Materiel')]
    #[ORM\JoinColumn(name: 'materiel_id', nullable: false)]
    public $materiel;
    #[ORM\Column(type: 'float')]
    public $cout;

}
?>
