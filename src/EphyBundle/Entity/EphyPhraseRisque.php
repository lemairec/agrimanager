<?php

namespace EphyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyPhraseRisque
 *
 * @ORM\Table(name="ephy_phrase_risque")
 * @ORM\Entity(repositoryClass="EphyBundle\Repository\EphyPhraseRisqueRepository")
 */
class EphyPhraseRisque
{
    /**
     * @ORM\Column(type="string", length=255)
     * @ORM\Id
     */
    public $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $description;

    /**
     * @ORM\Column(type="boolean")
     */
    public $cmr;
}
