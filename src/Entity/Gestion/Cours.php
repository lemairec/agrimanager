<?php

namespace App\Entity\Gestion;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Cours
 */
#[ORM\Table(name: 'cours')]
#[ORM\Entity(repositoryClass: 'App\Repository\CoursRepository')]
class Cours
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

    #[ORM\Column(type: 'string', length: 255)]
    public $produit;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    public $campagne;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    public $source;

    /**
     * @var float
     */
    #[ORM\Column(name: 'value', type: 'float')]
    public $value;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date', type: 'date')]
    public $date;

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }
}
