<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\AnalyseSol;
use App\Form\AnalyseSolType;


class AnalyseSolController extends CommonController
{
    /**
     * @Route("/analyse_sol", name="analyse_sols")
     */
    public function produitsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $analyseSols = $em->getRepository('App:AnalyseSol')
            ->findAll();

        return $this->render('Default/analyse_sols.html.twig', array(
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
            $analyse_sol = $em->getRepository('App:AnalyseSol')->findOneById($analyse_sol_id);
            $campagne = $analyse_sol->parcelle->campagne;
        }
        $parcelles =  $em->getRepository('App:Parcelle')->getAllForCampagne($campagne);
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
        return $this->render('Default/analyse_sol.html.twig', array(
            'form' => $form->createView(),
            'analyse_sol' => $analyse_sol,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,

        ));
    }
}
