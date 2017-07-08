<?php

namespace MeteoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/meteo")
     */
    public function indexAction()
    {
        return $this->render('MeteoBundle:Default:index.html.twig');
    }
}
