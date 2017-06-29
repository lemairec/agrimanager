<?php

namespace GestionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AgriBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;

use DateTime;

class DefaultController extends CommonController
{
    /**
     * @Route("/cours", name="cours")
     */
    public function coursAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $courss = $em->getRepository('GestionBundle:Cours')->getAllForCampagne($campagne);

        return $this->render('GestionBundle:Default:cours.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'courss' => $courss,
        ));
    }

    /**
     * @Route("/cours/new", name="cours_new")
     */
    public function coursNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->check_user();

        $courss = $em->getRepository('GestionBundle:Cours')->findByCampagne($campagne);
        if ($request->getMethod() == 'POST') {
            $em->getRepository('GestionBundle:Cours')->saveArray($this->company, $request->request->all());
            return $this->redirectToRoute('cours');
        }
        $date = new DateTime();
        return $this->render('GestionBundle:Default:cours_new.html.twig', array(
            'date' => $date->format("d-m-Y"),
            'courss' => $courss,
        ));
    }
}
