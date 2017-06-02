<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphySubstance
 *
 * @ORM\Table(name="ephy_substance")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\EphySubstanceRepository")
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
