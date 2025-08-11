<?php

namespace App\Entity\Iot;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: 'App\Repository\Iot\MoteurRepository')]
class Moteur
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

    #[ORM\Column(type: 'datetime', nullable: true)]
    public $last_update;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $my_last_value;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $ecart_temperature;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $is_auto;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Iot\Balise')]
    #[ORM\JoinColumn(nullable: true)]
    public $balise;

    #[ORM\Column(type: 'float', nullable: true)]
    public $last_temperature;
    public $desired;

    public $debug;

    public function calculate(){
        $this->debug = 'init';
        $this->desired = 0;
        if(!$this->is_auto){
            $this->debug = 'not auto';
            return;
        }
        if(!$this->balise){
            $this->debug = 'balise null';
            return;
        }
    
        $today = date("d.m.Y");

        $match_date = "";
        if($this->last_update){
            $match_date = $this->last_update->format('d.m.Y');
        }
        $is_ok = false;
        if($today == $match_date) {
            if($this->last_temperature > -50) {
                $is_ok = true;
            }
        }

        if(!$is_ok ){
            $this->debug = 'moteur ko';
            return;
        }
        
        $match_date = "";
        if($this->balise->last_update){
            $match_date = $this->balise->last_update->format('d.m.Y');
        }
        $is_ok = false;
        if($today == $match_date) {
            if($this->balise->last_temp > -50) {
                $is_ok = true;
            }
        }

        $is_ok = true;
        if(!$is_ok ){
            $this->debug = 'balise ko';
            return;
        }

        $balise_temp = $this->balise->last_temp;
        $temperature_ext = $this->last_temperature;
        $diff = $this->ecart_temperature;
        
        if($balise_temp > $temperature_ext + $diff){
            $this->debug = 'ok 1';
            $this->desired = 1;
        } else {
            $this->debug = 'ok 0';
            $this->desired = 0;
        }
    }

     
    public function __toString ( ){
        return $this->name." ".$this->label;
    }
    
    public $is_ok = false;
}
