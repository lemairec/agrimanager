<?php

namespace App\Entity\Agrigps;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\Agrigps\JobGpsRepository')]
class JobGps
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'datetime')]
    public $dateBegin;

    #[ORM\Column(type: 'datetime')]
    public $dateEnd;

    #[ORM\Column(type: 'float')]
    public $surface;

    #[ORM\Column(type: 'text')]
    public $job = "";

    #[ORM\Column(type: 'text', nullable: true)]
    public $debug = "";

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    public $label;

    /**
     * @var string
     */
    #[ORM\Column(name: 'user_email', type: 'string', length: 255, nullable: true)]
    public $userEmail;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\User')]
    #[ORM\JoinColumn(nullable: true)]
    public $user;

}
