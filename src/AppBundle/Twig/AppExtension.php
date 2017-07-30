<?php

// src/AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AppExtension extends \Twig_Extension
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;


    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage    $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('showLitre', array($this, 'showLitreFilter')),
            new \Twig_SimpleFilter('showDate', array($this, 'showDateFilter')),
            new \Twig_SimpleFilter('showHa', array($this, 'showHaFilter')),
            new \Twig_SimpleFilter('showEurUnity', array($this, 'showEurUnityFilter')),
        );
    }

    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    public function showDateFilter($date)
    {
        return $date->format('d/m/y');
    }

    public function showUnityFilter($number, $unity, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        $value = number_format($number, $decimals, $decPoint, $thousandsSep);
        if($this->getUser()->show_unity){
            $value = $value." ".$unity;
        }

        return $value;
    }

    public function showLitreFilter($number, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        return $this->showUnityFilter($number, "litres", $decimals, $decPoint, $thousandsSep);
    }

    public function showHaFilter($number, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        return $this->showUnityFilter($number, "ha", $decimals, $decPoint, $thousandsSep);
    }

    public function showEurUnityFilter($number, $unity='u', $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        return $this->showUnityFilter($number, "€/".$unity, $decimals, $decPoint, $thousandsSep);
    }



    public function getName()
    {
        return 'app_extension';
    }
}
