<?php

namespace EphyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EphyProduit
 *
 * @ORM\Table(name="ephy_produit")
 * @ORM\Entity(repositoryClass="EphyBundle\Repository\EphyProduitRepository")
 */
class EphyProduit
{
    /**
     * @var string
     * @ORM\Column(name="amm", type="string")
     */
    public $amm;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var string
     * @ORM\Column(name="unity", type="string", length=255)
     */
    public $unity = "unite";

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $society;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $typeCommercial;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $gammeUsage;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="complete_name", type="string", length=255)
     */
    public $completeName;

    /**
    * @ORM\OneToMany(targetEntity="EphyBundle\Entity\EphySubstanceProduit", mappedBy="ephyproduit",cascade={"persist"})
    */
    public $substances;

    /**
    * @ORM\ManyToMany(targetEntity="EphyBundle\Entity\EphyPhraseRisque")
    * @ORM\JoinTable(
     *  name="ephy_phrase_risques",
     *  joinColumns={
     *      @ORM\JoinColumn(name="produit", referencedColumnName="complete_name")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="phrase", referencedColumnName="id")
     *  }
     * )
    */
    public $phraseRisques;

    /**
    * @ORM\OneToMany(targetEntity="EphyBundle\Entity\EphyCommercialName", mappedBy="ephyproduit",cascade={"persist"})
    */
    public $commercialeNames;


    public function __toString ( ){
        return $this->completeName;
    }

    public function addPhraseRisque($phrase){
        foreach ($this->phraseRisques as $p) {
            if($p == $phrase){
                return;
            }
        }
        $this->phraseRisques[] = $phrase;
    }

    public function isCMR(){
        foreach ($this->phraseRisques as $p) {
            if($p->cmr){
                return true;
            }
        }
        return false;
    }
}
