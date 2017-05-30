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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
}
