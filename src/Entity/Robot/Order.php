<?php

namespace App\Entity\Robot;

use App\Repository\Robot\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'robot_order')]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 255)]
    public $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $type;

    #[ORM\Column(type: 'datetime')]
    public $d_create;

    #[ORM\Column(type: 'string')]
    public $status = "wait"; //wait //doing //done

    #[ORM\ManyToOne(targetEntity: Robot::class)]
    #[ORM\JoinColumn(nullable: false)]
    public $robot;

    #[ORM\Column(type: 'integer')]
    public $perc = 0;

    #[ORM\Column(type: 'json', nullable: true)]
    public $params;

    public function getEps32(){
        if($this->type == "LINEAB"){
            $res = sprintf("\$LINEAB,%d, %.7f,%.7f,%.7f,%.7f,*", $this->id, $this->params["a_lat"], $this->params["a_lon"], $this->params["b_lat"], $this->params["b_lon"]);
            return $res;
        }
        if($this->type == "CURVEAB"){
            $res = sprintf("\$CURVEAB,%d", $this->id);
            foreach ($this->params["points"] as $p) {
                $res = $res.sprintf(",%.7f,%.7f", $p[0], $p[1]);
            }
            $res = $res.",*";
            return $res;
        }
        return "\$".$this->type.",*";;
    }
}
