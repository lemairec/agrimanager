<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphySubstance
 *
 * @ORM\Entity(repositoryClass="App\Repository\EphySubstanceRepository")
 * @ORM\Table(name="ephy_substance")
 */
class EphySubstance
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=100)
     */
     public $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
     public $id;
}
