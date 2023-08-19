<?php

namespace App\Entity\Robot;

use App\Repository\Robot\RobotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RobotRepository::class)]
class Robot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 255)]
    public $name;

    #[ORM\Column(type: 'json', nullable: true)]
    public $last_data = [];

    #[ORM\Column(type: 'datetime', nullable: true)]
    public $last_update;

    #[ORM\Column(type: 'text', nullable: true)]
    public $config;

    #[ORM\Column(type: 'boolean', nullable: true)]
    public $reset = false;

    public $is_connected = true;



    public function __toString ( ){
        return $this->name;
    }
}
