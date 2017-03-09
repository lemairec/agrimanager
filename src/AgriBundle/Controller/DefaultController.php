<?php

namespace AgriBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AgriBundle\Entity\Ilot;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $advertRepository = $em->getRepository('AgriBundle:Company');
        return $this->render('AgriBundle:Default:index.html.twig');
    }

    /**
     * @Route("/ilots")
     */
    public function ilotsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ilots = $em->getRepository('AgriBundle:Ilot')->findAll();
        $sum_ilots = array_reduce($ilots, function($i, $obj)
        {
                return $i += $obj->surface;
        });
        echo $sum_ilots;

        return $this->render('AgriBundle:Default:ilots.html.twig', array(
                    'ilots' => $ilots,
                    'sum_ilots' => $sum_ilots,
                        ));
    }

    /**
     * @Route("/init")
     */
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ilot = new Ilot();
        $ilot->surface = 32.94;
        $ilot->name = "chemin du mesnil";
        $em->persist($ilot);
        $ilot = new Ilot();
        $ilot->surface = 5.68;
        $ilot->name = "chemin des canons";
        $em->persist($ilot);
        $ilot = new Ilot();
        $ilot->surface = 9.68;
        $ilot->name = "la noue balinet";
        $em->persist($ilot);
        $ilot = new Ilot();
        $ilot->surface = 3;
        $ilot->name = "les holles galant";
        $em->persist($ilot);
        $ilot = new Ilot();
        $ilot->surface = 19.6;
        $ilot->name = "batterie moucherie";
        $em->persist($ilot);
        $ilot = new Ilot();
        $ilot->surface = 5.54;
        $ilot->name = "cote merlan";
        $em->persist($ilot);
        $em->flush();
        return new Response ('Ok');
    }
}
