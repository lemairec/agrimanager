<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Materiel
 */
#[ORM\Table(name: 'materiel')]
#[ORM\Entity(repositoryClass: 'App\Repository\MaterielRepository')]
class Materiel
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
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    public $name;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date_achat', type: 'date', nullable: true)]
    public $dateAchat;

    /**
     * @var int
     */
    #[ORM\Column(name: 'annee', type: 'integer')]
    public $annee;

    /**
     * @var string
     */
    #[ORM\Column(name: 'caracteristique', type: 'string', length: 255, nullable: true)]
    public $caracteristique;

    /**
     * @var string
     */
    #[ORM\Column(name: 'comment', type: 'string', length: 255, nullable: true)]
    public $comment;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Company')]
    #[ORM\JoinColumn(nullable: false)]
    public $company;

    public function __toString ( ){
        return $this->name;
    }
}
