<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Commercialisation
 *
 * @ORM\Entity(repositoryClass="App\Repository\CommercialisationRepository")
 * @ORM\Table(name="commercialisation")
 */
class Commercialisation
{

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campagne")
     * @ORM\JoinColumn(nullable=false)
     */
    public $campagne;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Culture")
     * @ORM\JoinColumn(nullable=false)
     */
    public $culture;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * accompte, complement
     */
    public $type;

    /**
     * @var float
     *
     * @ORM\Column(name="qty", type="float")
     */
    public $qty;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    public $price;

    /**
     * @var float
     *
     * @ORM\Column(name="price_total", type="float")
     */
    public $price_total;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    public $comment;

}
