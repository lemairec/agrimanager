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
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="appartement_file", fileNameProperty="factureFileName")
     *
     * @var File
     */
    private $factureFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $factureFileName;

    /**
    * @var \DateTime
    *
    * @ORM\Column(type="datetime", nullable=true)
    */
    public $updatedAt;


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $doc
     */
    public function setFactureFile(?File $factureFile = null): void
    {

        $this->updatedAt = new \DateTime('now');
        $this->factureFile = $factureFile;
    }

    public function getFactureFile(): ?File
    {
        return $this->factureFile;
    }

    public function setFactureFileName(?string $factureFileName): void
    {
        $this->factureFileName = $factureFileName;
    }

    public function getFactureFileName(): ?string
    {
        return $this->factureFileName;
    }

    function getDateStr(){
        return $this->date->format(' d/m/y');
    }
}
