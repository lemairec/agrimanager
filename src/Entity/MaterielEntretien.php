<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\MaterielEntretien;

/**
 * MaterielEntretien
 */
#[ORM\Table(name: 'materiel_entretien')]
#[ORM\Entity(repositoryClass: 'App\Repository\MaterielEntretienRepository')]
class MaterielEntretien
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Materiel')]
    #[ORM\JoinColumn(nullable: false)]
    public $materiel;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Company')]
    #[ORM\JoinColumn(nullable: false)]
    public $company;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date', type: 'date')]
    public $date;

    /**
     * @var int
     */
    #[ORM\Column(name: 'nb_heure', type: 'integer')]
    public $nbHeure;

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    public $name;

    /**
     * @var string
     */
    #[ORM\Column(name: 'comment', type: 'string', length: 255, nullable: true)]
    public $comment;

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }

}
