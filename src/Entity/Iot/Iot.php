<?php

namespace App\Entity\Iot;

use App\Repository\Iot\IotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: IotRepository::class)]
class Iot
{
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $name;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Company')]
    #[ORM\JoinColumn(nullable: false)]
    public $company;


    #[ORM\Column(type: 'string', length: 255)]
    public $label = "";

    #[ORM\Column(type: 'text', nullable: true)]
    public $description = "";

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $last_config = "";

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $last_version = "";
    
    #[ORM\Column(type: 'datetime', nullable: true)]
    public $last_update_config;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public $last_update;


    public $is_ok = false;
}
