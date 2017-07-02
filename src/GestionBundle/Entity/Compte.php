<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte
 *
 * @ORM\Table(name="compte")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\CompteRepository")
 */
class Compte
{
    /**
     * @var guid
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

    /**
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Campagne")
     * @ORM\JoinColumn(nullable=true)
     */
    public $campagne;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $type;

    /**
     * @var float
     *
     * @ORM\Column(name="previsionnel", type="float")
     */
    public $previsionnel = 0;

    /**
    * @ORM\OneToMany(targetEntity="GestionBundle\Entity\Ecriture", mappedBy="compte",cascade={"persist"})
    */
    public $ecritures;

    public function getPrice ( ){
        $res = 0;
        foreach($this->ecritures as $e){
            $res += $e->value;
        }
        return $res;
    }

    public function __toString ( ){
        return $this->name;
    }
}
