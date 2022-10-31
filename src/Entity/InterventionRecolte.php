<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="intervention_recolte")
 */
class InterventionRecolte
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention", inversedBy="recoltes")
     * @ORM\JoinColumn(name="intervention_id", referencedColumnName="id", nullable=false)
     */
    public $intervention;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable=true)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicule", type="string", length=255,nullable=true)
     */
    public $vehicule;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $espece;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $poid_total;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    public $tare;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    public $caracteristiques;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    public $poid_norme;

    static function getStaticCarateristiques($caracteristiques){
        $res = "";
        if($caracteristiques){
            foreach($caracteristiques as $key => $value){
                if($res != ""){
                    $res = $res."; ";
                }
                //$res = $res.$key." ".round($value, 2);
            }
        }
        return $res;
    }

    public function getCarateristiques(){
        return $this->getStaticCarateristiques($this->caracteristiques);
    }

}

?>
