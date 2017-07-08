<?php

namespace MeteoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeteoPrevision
 *
 * @ORM\Table(name="meteo_prevision")
 * @ORM\Entity(repositoryClass="MeteoBundle\Repository\MeteoPrevisionRepository")
 */
class MeteoPrevision
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
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    public $city;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255)
     */
    public $source;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=25500)
     */
    public $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    public $date_utc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    public $date_update_utc;
}
