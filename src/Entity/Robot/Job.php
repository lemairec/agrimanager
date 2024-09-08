<?php

namespace App\Entity\Robot;

use App\Repository\Robot\JobRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobRepository::class)]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 255)]
    public $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $type;

    #[ORM\Column(type: 'json')]
    public $params = [];

    public $params_json = "";

    public $offset = 0;

    public $inrows = true;

    public function getEps32(){
        if($this->type == "LINEAB"){
            $res = sprintf("\$LINEAB,999, %.7f,%.7f,%.7f,%.7f,%d,*", $this->params["a_lat"], $this->params["a_lon"], $this->params["b_lat"], $this->params["b_lon"], $this->params["direction"]);
            return $res;
        }
        if($this->type == "CURVEAB"){
            $res = sprintf("\$CAB_B,*\n");
            foreach ($this->params["points"] as $p) {
                $res = $res.sprintf("\$CAB_P,%.7f,%.7f,*\n", $p[0], $p[1]);
            }
            $res = $res.sprintf("\$CAB_E,%d,%d,*\n",999, $this->params["direction"]);

            return $res;
        }
        return $this->type;
    }
}
