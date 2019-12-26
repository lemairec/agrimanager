<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyPhraseRisque
 *
 * @ORM\Entity(repositoryClass="App\Repository\EphyPhraseRisqueRepository")
 * @ORM\Table(name="ephy_phrase_risque")
 */
class EphyPhraseRisque
{
    /**
     * @ORM\Column(type="string", length=100)
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
