<?php

namespace AgriBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity(repositoryClass="AgriBundle\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @var int
     *
     * @ORM\Column(name="amm", type="integer")
     */
    public $amm;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="complete_name", type="string", length=255)
     */
    public $completeName;

    /**
    /**
     * @var string
     *
     * @ORM\Column(name="no_ephy", type="string", length=255)
     * @ORM\Id
     */
    public $no_ephy;

    public function getCompletUrl(){
        return 'http://e-phy.agriculture.gouv.fr/spe/'.$this->no_ephy.'htm';
    }

}

