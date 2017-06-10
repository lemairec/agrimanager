<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AgriBundle\Entity\MaterielEntretien;

/**
 * MaterielEntretien
 *
 * @ORM\Table(name="materiel_entretien")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\MaterielEntretienRepository")
 */
class MaterielEntretien
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_heure", type="integer")
     */
    public $nbHeure;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer")
     */
    public $value;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Materiel")
     * @ORM\JoinColumn(nullable=false)
     */
    public $materiel;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;
}
