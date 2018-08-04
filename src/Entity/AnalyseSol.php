<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * AnalyseSol
 *
 * @ORM\Table(name="analyse_sol")
 * @ORM\Entity(repositoryClass="App\Repository\AnalyseSolRepository")
 * @Vich\Uploadable
 */
class AnalyseSol
{
    /**
     * @var guid
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
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
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="analyse_sol", fileNameProperty="docName")
     *
     * @var File
     */
    private $docFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $docName;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $doc
     */
    public function setDocFile(?File $doc = null): void
    {
        $this->docFile = $doc;
    }

    public function getDocFile(): ?File
    {
        return $this->docFile;
    }

    public function setDocName(?string $docName): void
    {
        $this->docName = $docName;
    }

    public function getDocName(): ?string
    {
        return $this->docName;
    }

    function get_date(){
        return $this->date->format(' d/m/y');
    }
}
