<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
/**
 * AppartementOperation
 *
 * @ORM\Table(name="appartement_operation")
 * @ORM\Entity(repositoryClass="App\Repository\AppartementOperationRepository")
 * @Vich\Uploadable
 */
class AppartementOperation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    public $type;

    /**
     * @var int
     *
     * @ORM\Column(name="annee", type="integer", length=255)
     */
    public $annee;

    public $sum;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    public $value;

    /**
    * @ORM\OneToOne(targetEntity="Document",cascade={"persist"})
    */
    public $doc;

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }
}
