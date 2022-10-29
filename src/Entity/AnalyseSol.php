<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Uid\Uuid;

/**
 * AnalyseSol
 *
 * @ORM\Entity(repositoryClass="App\Repository\AnalyseSolRepository")
 * @ORM\Table(name="analyse_sol")
 * @Vich\Uploadable
 */
class AnalyseSol
{
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     */
    public $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Parcelle")
     * @ORM\JoinColumn(nullable=false)
     */
    public $parcelle;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $ph = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $mo = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $p = 0;
    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $k = 0;
    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $mg = 0;

    /**
    * @ORM\OneToOne(targetEntity="Document",cascade={"persist"})
    */
    public $doc;

    function getDatetimeStr(){
        return $this->date->format(' d/m/y');
    }
}
