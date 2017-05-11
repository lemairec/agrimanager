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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Campagne")
     * @ORM\JoinColumn(nullable=false)
     */
    public $campagne;

    /**
     * @var float
     *
     * @ORM\Column(name="surface", type="float")
     */
    public $surface;


    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Ilot")
     * @ORM\JoinColumn(nullable=false)
     */
    public $ilot;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="complete_name", type="string")
     */
    public $completeName;

    /**
     * @var string
     *
     * @ORM\Column(name="culture", type="string")
     */
    public $culture;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=2048)
     */
    public $comment;

}
