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
     * @ORM\ManyToOne(targetEntity="AgriBundle\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    public $company;

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

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    public $comment;


    public function __toString ( ){
        return $this->name." - ".number_format($this->surface,2)." ha";
    }
}
