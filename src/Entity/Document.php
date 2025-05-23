<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Uid\Uuid;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: 'App\Repository\DocumentRepository')]
class Document
{
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Company')]
    #[ORM\JoinColumn(nullable: false)]
    public $company;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    public $name;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\DocumentDirectory')]
    #[ORM\JoinColumn(nullable: true)]
    public $directory;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'date', nullable: true)]
    public $date;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="document", fileNameProperty="docName")
     *
     * @var File
     */
    private $docFile;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $docName;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    public $updatedAt;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date_export', type: 'date', nullable: true)]
    public $dateExport;

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

    public function getDocMyFileName(){
        $str = $this->name;
        $str = str_replace(" - ", '_', $str);
        $str = str_replace(' ', '_', $str);
        $str = str_replace('-', '', $str);
        $str = str_replace(',', '', $str);
        $str = str_replace('/', '_', $str);
        $str = str_replace('&', '_', $str);
        $str = str_replace('é', 'e', $str);
        $str = str_replace('è', 'e', $str);
        $str = strtolower($str);

        return $this->date->format('Ymd').'_'.$str.'.pdf';
    }

    public function __toString ( ){
        return "toto";
    }
}
