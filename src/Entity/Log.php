<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 * @ORM\Table(name="log")
 */
 class Log
 {
     /**
      * @ORM\Id()
      * @ORM\GeneratedValue()
      * @ORM\Column(type="integer")
      */
     public $id;

     /**
      * @var \DateTime
      *
      * @ORM\Column(name="datetime", type="datetime")
      */
     public $date;

     /**
      * @ORM\ManyToOne(targetEntity="App\Entity\User")
      * @ORM\JoinColumn(nullable=false)
      */
     public $user;

     /**
      * @ORM\ManyToOne(targetEntity="App\Entity\Company")
      * @ORM\JoinColumn(nullable=false)
      */
     public $company;

     /**
      * @var string
      *
      * @ORM\Column(name="description", type="string", length=255)
      */
     public $description;

     /**
      * @var string
      *
      * @ORM\Column(name="detail", type="text", nullable=true)
      */
     public $detail;
 }
