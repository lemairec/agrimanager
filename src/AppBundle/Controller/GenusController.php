<?php
namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class GenusController
{
    /**
 *      * @Route("/genus")
 *           */
    public function showAction()
    {
        return new Response('Under the sea!');
    }
}
