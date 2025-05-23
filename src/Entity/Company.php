<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Company
 */
#[ORM\Table(name: 'company')]
#[ORM\Entity(repositoryClass: 'App\Repository\CompanyRepository')]
class Company
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

    /**
     * @var string
     */
    #[ORM\Column(name: 'adresse', type: 'string', length: 255, nullable: true)]
    public $adresse;

    /**
     * @var string
     */
    #[ORM\Column(name: 'city', type: 'string', length: 255, nullable: true)]
    public $city;

    /**
     * @var string
     */
    #[ORM\Column(name: 'city_code', type: 'string', length: 255, nullable: true)]
    public $cityCode;


    /**
     * @var string
     */
    #[ORM\Column(name: 'site1_name', type: 'string', length: 255)]
    public $site1_name;


     /**
     * @var string
     */
    #[ORM\Column(name: 'site1_url', type: 'string', length: 255)]
    public $site1_url;

    /**
     * @var string
     */
    #[ORM\Column(name: 'meto_city', type: 'string', length: 255, nullable: true)]
    public $meteoCity;

    #[ORM\JoinTable(name: '_fos_user_user_company')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'company_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'App\Entity\User')]
    public $users;

    public function __toString ( ){
        return $this->id." ".$this->name;
    }

}
