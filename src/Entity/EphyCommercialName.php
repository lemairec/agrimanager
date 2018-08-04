<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyCommercialName
 *
 * @ORM\Table(name="ephy_commercial_name")
 * @ORM\Entity(repositoryClass="App\Repository\EphyCommercialNameRepository")
 */
class EphyCommercialName
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="complete_name", type="string", length=255)
     */
    public $completeName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EphyProduit", inversedBy="commercialeNames")
     * @ORM\JoinColumn(name="ephyproduit", referencedColumnName="complete_name")
     */
    public $ephyproduit;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    public function getAmm(){
        return $this->ephyproduit->amm;
    }

    public function getColor(){
        return $this->ephyproduit->getColor();
    }


}
