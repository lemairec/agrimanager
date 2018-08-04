<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use AppBundle\Controller\CommonController;

use AppBundle\Entity\AnalyseSol;
use AppBundle\Form\AnalyseSolType;


class AnalyseSolController extends CommonController
{
    /**
     * @Route("/analyse_sol", name="analyse_sols")
     */
    public function produitsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $analyseSols = $em->getRepository('AppBundle:AnalyseSol')
            ->findAll();

        return $this->render('AppBundle:Default:analyse_sols.html.twig', array(
            'analyse_sols' => $analyseSols,
        ));
    }

    /**
     * @Route("/analyse_sol/{analyse_sol_id}", name="analyse_sol")
     **/
    public function produitEditAction($analyse_sol_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        if($analyse_sol_id == '0'){
            $analyse_sol = new AnalyseSol();
        } else {
            $analyse_sol = $em->getRepository('AppBundle:AnalyseSol')->findOneById($analyse_sol_id);
        }
        $parcelles =  $em->getRepository('AppBundle:Parcelle')->getAllForCampagne($campagne);
        $form = $this->createForm(AnalyseSolType::class, $analyse_sol, array(
            'parcelles' => $parcelles
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $analyse_sol->campagne = $campagne;
            $em->persist($analyse_sol);
            $em->flush();
            return $this->redirectToRoute('analyse_sols');
        }
        return $this->render('AppBundle:Default:analyse_sol.html.twig', array(
            'form' => $form->createView(),
            'analyse_sol' => $analyse_sol,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,

        ));
    }
}
