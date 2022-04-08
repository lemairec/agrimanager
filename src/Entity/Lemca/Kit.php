<?php

namespace App\Entity\Lemca;

use App\Repository\Lemca\KitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=KitRepository::class)
 */
class Kit
{
     /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $client;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $option;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $revendeur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $comment;

    /**
     * @ORM\OneToMany(targetEntity=Camera::class, mappedBy="kit")
     */
    public $cameras;

    /**
     * @ORM\OneToMany(targetEntity=Panel::class, mappedBy="kit")
     */
    public $panels;

    public function __toString ( ){
        return $this->id." ".$this->client;
    }
}
