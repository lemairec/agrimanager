<?php

// src/App/Twig/AppExtension.php
namespace App\Twig;

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
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{

    public function getFilters()
    {
        return array(
            new TwigFilter('showDecimal', array($this, 'showDecimal')),
            new TwigFilter('showInt', array($this, 'showInt')),
            new TwigFilter('showUnity', array($this, 'showUnityFilter')),
            new TwigFilter('showUnityHa', array($this, 'showUnityHaFilter')),
            new TwigFilter('showLitre', array($this, 'showLitreFilter')),
            new TwigFilter('showDate', array($this, 'showDateFilter')),
            new TwigFilter('showDatetime', array($this, 'showDatetimeFilter')),
            new TwigFilter('showHa', array($this, 'showHaFilter')),
            new TwigFilter('showPercent', array($this, 'showPercentFilter')),
            new TwigFilter('showEur', array($this, 'showEurFilter')),
            new TwigFilter('showEurUnity', array($this, 'showEurUnityFilter')),
            new TwigFilter('showIsoDate', array($this, 'showIsoDateFilter')),
            new TwigFilter('my_path', array($this, 'my_path')),


        );
    }

    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }


    public function showUnityFilter($number, $unity, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        if($number != 0){
            $value = number_format($number, $decimals, $decPoint, $thousandsSep);
            if($this->getUser()->show_unity){
                $value = $value." ".$unity;
            }

            return $value;
        } else {
            if($this->getUser()->show_unity){
                return "-";
            } else {
                return "";
            }
        }

    }

    public function showUnityHaFilter($number, $unity, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        return $this->showUnityFilter($number, $unity."/ha", $decimals, $decPoint, $thousandsSep);
    }

    public function showInt($number, $decPoint = '.', $thousandsSep = ' ')
    {
        $value = number_format($number, 0, $decPoint, $thousandsSep);
        return $value;
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

    public function showDatetimeFilter($date)
    {
        if($date){
            return $date->format('d/m/Y H:i');
        } else {
            return "-";
        }
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
