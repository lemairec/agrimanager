<?php

namespace App\Entity\Ruche;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Ruche\ActionRepository")
 * @ORM\Table(name="r_action")
 */
class Action
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ruche\Essaim")
     */
    public $essaim;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ruche\Ruche")
     * @ORM\JoinColumn(nullable=false)
     */
    public $ruche;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ruche\Rucher")
     * @ORM\JoinColumn(nullable=false)
     */
    public $rucher;
}
