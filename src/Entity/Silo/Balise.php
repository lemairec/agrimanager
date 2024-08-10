<?php

namespace App\Entity\Silo;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: 'App\Repository\Silo\BaliseRepository')]
class Balise
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

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    public $name;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Company')]
    #[ORM\JoinColumn(nullable: false)]
    public $company;


    #[ORM\Column(type: 'string', length: 255)]
    public $label = "";

    #[ORM\Column(type: 'text', nullable: true)]
    public $description = "";

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $unity = "°C";

    #[ORM\Column(type: 'datetime', nullable: true)]
    public $last_update;

    #[ORM\Column(type: 'float', nullable: true)]
    public $last_temp;

    #[ORM\Column(type: 'float', nullable: true)]
    public $offset = 0;

    #[ORM\Column(type: 'float', nullable: true)]
    public $scale = 0;

    #[ORM\Column(type: 'float', nullable: true)]
    public $last_calculate;

    public function calculate(){
        $this->last_calculate = $this->last_temp;
        if($this->offset){
            $this->last_calculate = $this->last_calculate - $this->offset;
        }
        if($this->scale){
            $this->last_calculate = $this->last_calculate * $this->scale;
        }
    }

    public function calculFor($value){
        $res = $value;
        if($this->offset){
            $res = $res - $this->offset;
        }
        if($this->scale){
            $res = $res * $this->scale;
        }
        return $res;
    }

    public $is_ok = false;
}
