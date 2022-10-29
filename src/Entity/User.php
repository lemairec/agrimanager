<?php
// src/App/Entity/User.php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="_fos_user")
 */
class User extends BaseUser
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @ORM\JoinTable(name="_fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    public $groups;

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
        // your own logic
    }
}
