<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\ContactRepository')]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime')]
    public $datetime;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    public $email;


    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    public $text;
}
