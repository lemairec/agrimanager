<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @Vich\Uploadable
 */
class Document
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
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $repository;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="document", fileNameProperty="docName")
     *
     * @var File
     */
    private $docFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $docName;

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
}