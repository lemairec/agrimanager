<?php

namespace App\Entity\Gestion;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Ecriture
 */
#[ORM\Table(name: 'ecriture')]
#[ORM\Entity(repositoryClass: 'App\Repository\Gestion\EcritureRepository')]
class Ecriture
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

    /**
     * @var float
     */
    #[ORM\Column(name: 'value', type: 'float')]
    public $value;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Gestion\Compte', inversedBy: 'ecritures')]
    #[ORM\JoinColumn(nullable: false)]
    public $compte;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Campagne')]
    #[ORM\JoinColumn(nullable: true)]
    public $campagne;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Gestion\Operation', inversedBy: 'ecritures')]
    #[ORM\JoinColumn(nullable: false)]
    public $operation;

}
