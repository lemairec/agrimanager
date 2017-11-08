<?php

namespace AnnonceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Annonce
 *
 * @ORM\Table(name="annonce")
 * @ORM\Entity(repositoryClass="AnnonceBundle\Repository\AnnonceRepository")
 */
class Annonce
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    public $url;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $description;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    public $price;

    /**
     * @var datetime
     *
     * @ORM\Column(type="datetime")
     */
    public $lastView;

    /**
     * @var datetime
     *
     * @ORM\Column(type="datetime")
     */
    public $firstView;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $clientId;
    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    public $image;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $new;

    /**
     * @var string
     *
     * @ORM\Column(name="log", type="string", length=2550)
     */
    public $log;
}
