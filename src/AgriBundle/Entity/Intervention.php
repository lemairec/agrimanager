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
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Campagne")
     * @ORM\JoinColumn(nullable=false)
     */
    public $campagne;


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
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=2048, nullable=true)
     */
    public $comment;

    /**
    * @ORM\OneToMany(targetEntity="AgriBundle\Entity\InterventionParcelle", mappedBy="intervention",cascade={"persist"})
    */
    public $parcelles;

    /**
    * @ORM\OneToMany(targetEntity="AgriBundle\Entity\InterventionProduit", mappedBy="intervention",cascade={"persist"})
    */
    public $produits;

    /**
     * @var float
     *
     * @ORM\Column(name="surface", type="float")
     */
    public $surface;

    public function __construct() {
        $this->parcelles = new ArrayCollection();
        $this->produits = new ArrayCollection();
    }

    function getPriceHa(){
        $res = 0;
        foreach($this->produits as $p){
            $res += $p->getPriceHa();
        }
        return $res;
    }

    function get_date(){
        return $this->date->format(' d/m/Y');
    }
}
