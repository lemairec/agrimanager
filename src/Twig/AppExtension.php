<?php

// src/App/Twig/AppExtension.php
namespace App\Twig;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppExtension extends \Twig_Extension
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;


    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage    $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, ContainerInterface $container)
    {
        $this->tokenStorage = $tokenStorage;
        $this->container = $container;

    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('showDecimal', array($this, 'showDecimal')),
            new \Twig_SimpleFilter('showUnity', array($this, 'showUnityFilter')),
            new \Twig_SimpleFilter('showUnityHa', array($this, 'showUnityHaFilter')),
            new \Twig_SimpleFilter('showLitre', array($this, 'showLitreFilter')),
            new \Twig_SimpleFilter('showDate', array($this, 'showDateFilter')),
            new \Twig_SimpleFilter('showDateTime', array($this, 'showDateTimeFilter')),
            new \Twig_SimpleFilter('showHa', array($this, 'showHaFilter')),
            new \Twig_SimpleFilter('showPercent', array($this, 'showPercentFilter')),
            new \Twig_SimpleFilter('showEur', array($this, 'showEurFilter')),
            new \Twig_SimpleFilter('showEurUnity', array($this, 'showEurUnityFilter')),
            new \Twig_SimpleFilter('showIsoDate', array($this, 'showIsoDateFilter')),
            new \Twig_SimpleFilter('my_path', array($this, 'my_path')),


        );
    }

    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }


    public function showUnityFilter($number, $unity, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        $value = number_format($number, $decimals, $decPoint, $thousandsSep);
        if($this->getUser()->show_unity){
            $value = $value." ".$unity;
        }

        return $value;
    }

    public function showUnityHaFilter($number, $unity, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        return $this->showUnityFilter($number, $unity."/ha", $decimals, $decPoint, $thousandsSep);
    }

    public function showDecimal($number, $decimals = 2, $decPoint = '.', $thousandsSep = ' ')
    {
        $value = number_format($number, $decimals, $decPoint, $thousandsSep);
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

    public function showEurUnityFilter($number, $unity='u', $showZero=true, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        if($number != 0 || $showZero){
            return $this->showUnityFilter($number, "€/".$unity, 2, ',', ' ');
        } else {
            if($this->getUser()->show_unity){
                return "-";
            } else {
                return "";
            }
        }
    }

    public function showEurFilter($number, $showZero=true, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        if($number != 0 || $showZero){
            return $this->showUnityFilter($number, "€", $decimals, $decPoint, $thousandsSep);
        } else {
            if($this->getUser()->show_unity){
                return "-";
            } else {
                return "";
            }
        }
    }

    public function showPercentFilter($number, $decimals = 0, $decPoint = ',', $thousandsSep = ' ')
    {
        if($this->getUser()->show_unity){
            return number_format($number*100, $decimals, $decPoint, $thousandsSep)." %";
        } else {
            $decimals += 2;
            return number_format($number, $decimals, $decPoint, $thousandsSep);
        }
    }

    public function showIsoDateFilter($date)
    {
        return $date->format(' Y-m-d');
    }

    public function showDateFilter($date)
    {
        return $date->format('d/m/y');
    }

    public function showDateTimeFilter($date)
    {
        return $date->format('d/m/Y H:i');
    }

    public function my_path($label, $route, $parameters = array())
    {
        if($this->getUser()->show_unity){
            //$url = $this->generateUrl('fos_user_profile_edit');
            $url = $this->container->get('router')->generate($route, $parameters);
            return "<a href=\"".$url."\">".$label."</a>";
        } else {
            return $label;
        }
    }



    public function getName()
    {
        return 'app_extension';
    }
}
