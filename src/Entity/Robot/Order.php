<?php

namespace App\Entity\Robot;

use App\Repository\Robot\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="robot_order")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $type;

    /**
     * @ORM\Column(type="datetime")
     */
    public $d_create;

    /**
     * @ORM\Column(type="boolean")
     */
    public $done = false;

    /**
     * @ORM\ManyToOne(targetEntity=Robot::class)
     * @ORM\JoinColumn(nullable=false)
     */
    public $robot;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    public $params;
    
}
