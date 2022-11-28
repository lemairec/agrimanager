<?php
// src/App/Entity/Group.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: '_fos_group')]
#[ORM\Entity(repositoryClass: 'App\Repository\GroupRepository')]
class Group
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    public $name;

    /**
     * @var string
     */
    #[ORM\Column(name: 'roles', type: 'array')]
    public $roles;

    public function __toString ( ){
        return $this->name;
    }

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->roles = [];
    }
}
