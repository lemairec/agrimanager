<?php

namespace App\Entity\Lemca;

use App\Repository\Lemca\CameraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CameraRepository::class)
 */
class Camera
{
     /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $id;

     /**
     * @ORM\Column(type="string", length=255)
     */
    public $no_fabricant;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $ip;

    /**
     * @ORM\ManyToOne(targetEntity=Kit::class, inversedBy="cameras")
     */
    public $kit;
n
    public function getKitStr()
    {
        if($this->kit){
            return $this->kit->client;
        }
        return "";
    }

    public function setKit(?Kit $kit): self
    {
        $this->kit = $kit;

        return $this;
    }
}
