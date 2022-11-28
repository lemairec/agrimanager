<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyCommercialName
 */
#[ORM\Table(name: 'ephy_commercial_name')]
#[ORM\Entity(repositoryClass: 'App\Repository\EphyCommercialNameRepository')]
class EphyCommercialName
{
    #[ORM\ManyToOne(targetEntity: 'App\Entity\EphyProduit', inversedBy: 'commercialeNames')]
    #[ORM\JoinColumn(name: 'ephyproduit', referencedColumnName: 'amm', nullable: false)]
    public $ephyproduit;

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string', length: 100)]
    #[ORM\Id]
    public $name;

    public function getAmm(){
        return $this->ephyproduit->amm;
    }

    public function getUnity(){
        return $this->ephyproduit->unity;
    }

    public function getColor(){
        return $this->ephyproduit->getColor();
    }

    public function getCompleteName(){
        return $this->ephyproduit->amm." - ".$this->name;
    }
}
