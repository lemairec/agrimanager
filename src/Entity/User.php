<?php
// src/App/Entity/User.php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: '_fos_user')]
#[ORM\Entity(repositoryClass: 'App\Repository\UserRepository')]
class User extends BaseUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public $id;

    #[ORM\JoinTable(name: '_fos_user_user_group')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'App\Entity\Group')]
    public $groups = [];

    public function getRoles(): array {
      $roles = $this->roles;

      foreach ($this->groups as $group) {
          $roles = array_merge($roles, $group->roles);
      }

      $roles[] = static::ROLE_DEFAULT;

      return array_unique($roles);
    }

    public $show_unity = true;


    public function __construct()
    {
        parent::__construct();
        $this->id = Uuid::v4();
        // your own logic
    }
}
