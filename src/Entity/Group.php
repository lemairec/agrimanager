<?php
// src/App/Entity/Group.php

namespace App\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="_fos_group")
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 */
class Group extends BaseGroup
{
    /**
     * @var guid
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $id;

    public function __toString ( ){
        return $this->name;
    }

    public function __construct()
    {
        $this->roles = [];
    }
}
