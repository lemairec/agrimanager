<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Deplacement
 */
#[ORM\Table(name: 'deplacement')]
#[ORM\Entity(repositoryClass: 'App\Repository\DeplacementRepository')]
class Deplacement
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

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Campagne')]
    #[ORM\JoinColumn(nullable: false)]
    public $campagne;

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    public $name;

    /**
     * @var string
     */
    #[ORM\Column(name: 'vehicule', type: 'string', length: 255)]
    public $vehicule;

    /**
     * @var string
     */
    #[ORM\Column(name: 'comment', type: 'string', length: 2048, nullable: true)]
    public $comment;

    /**
     * @var int
     */
    #[ORM\Column(name: 'km', type: 'integer')]
    public $km;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date', type: 'date')]
    public $date;
}
