<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Intervention
 *
 * @ORM\Table(name="intervention")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\InterventionRepository")
 */
class Intervention
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    public $type;

    /**
    * @ORM\OneToMany(targetEntity="AgriBundle\Entity\InterventionParcelle", mappedBy="intervention", fetch="EAGER")
    */
    public $parcelles;

    public function __construct() {
        $this->parcelles = new ArrayCollection();
    }

    function get_date(){
            return $this->date->format(' d/m/Y'); 
    }


}

