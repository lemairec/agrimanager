<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ilot
 *
 * @ORM\Table(name="ilot")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\IlotRepository")
 */
class Ilot
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
     * @var float
     *
     * @ORM\Column(name="surface", type="float")
     */
    public $surface;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    public $name;
}
