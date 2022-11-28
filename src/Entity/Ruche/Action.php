<?php

namespace App\Entity\Ruche;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'r_action')]
#[ORM\Entity(repositoryClass: 'App\Repository\Ruche\ActionRepository')]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'date')]
    public $date;

    #[ORM\Column(type: 'string', length: 255)]
    public $type;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $description;



    #[ORM\ManyToOne(targetEntity: 'App\Entity\Ruche\Essaim')]
    #[ORM\JoinColumn(nullable: false)]
    public $essaim;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Ruche\Ruche')]
    #[ORM\JoinColumn(nullable: false)]
    public $ruche;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Ruche\Rucher')]
    #[ORM\JoinColumn(nullable: false)]
    public $rucher;
}
