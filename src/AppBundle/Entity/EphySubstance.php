<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphySubstance
 *
 * @ORM\Table(name="ephy_substance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EphySubstanceRepository")
 */
class EphySubstance
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
     public $id;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
     public $name;
}
