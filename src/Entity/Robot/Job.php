<?php

namespace App\Entity\Robot;

use App\Repository\Robot\JobRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;

     /**
     * @ORM\Column(type="string", length=255)
     */
    public $job_type;

    /**
     * @ORM\Column(type="json")
     */
    public $params = [];

    public $params_json = "";
}
