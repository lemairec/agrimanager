<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * FactureFournisseur
 *
 * @ORM\Table(name="facture_fournisseur")
 * @ORM\Entity(repositoryClass="App\Repository\FactureFournisseurRepository")
 * @Vich\Uploadable
 */
class FactureFournisseur
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Campagne")
     * @ORM\JoinColumn(nullable=true)
     */
    public $campagne;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    public $date;

    /**
     * @var float
     *
     * @ORM\Column(name="montantHT", type="float")
     */
    public $montantHT;

    /**
     * @var float
     *
     * @ORM\Column(name="montantTTC", type="float")
     */
    public $montantTTC;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $type;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\Achat", mappedBy="facture")
    */
    public $achats;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte")
     * @ORM\JoinColumn(nullable=false)
     */
    public $compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte")
     * @ORM\JoinColumn(nullable=false)
     */
    public $banque;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="facture_file", fileNameProperty="factureFileName")
     *
     * @var File
     */
    private $factureFile;

    /**
     * @ORM\Column(type="string", name="brochure", length=255, nullable=true)
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

    public function __toString ( ){
        $res = $this->date->format("Y-m-d")."_".$this->name;
        return $res;
    }

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

    public function getFactureMyFileName(){
        if($this->factureFileName){
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
        } else {
            return "";
        }
    }

    function getAchatsTotal(){
        $achats_total = 0;
        foreach ($this->achats as $a) {
            $achats_total+=$a->price_total;
        }
        return $achats_total;
    }

    function getDateStr(){
        return $this->date->format('d/m/y');
    }

    function getCampagneStr(){
        if($this->campagne){
            return $this->campagne->name;
        } else {
            return "";
        }
    }

    function getPercentTVA(){
        if($this->montantHT != 0){
            return (($this->montantTTC-$this->montantHT)/$this->montantHT);
        } else {
            return 0;
        }

    }

}
