<?php

namespace App\Entity\Lemca;

use App\Repository\Lemca\PanelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PanelRepository::class)
 */
class Panel
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $no_fabricant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $generation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $comment;

    /**
     * @ORM\ManyToOne(targetEntity=Kit::class, inversedBy="panels")
     */
    public $kit;
}