<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\AnalyseSol;
use App\Entity\DocumentDirectory;
use App\Form\AnalyseSolType;



class AnalyseSolController extends CommonController
{
    #[Route(path: '/analyse_sols', name: 'analyse_sols')]
    public function produitsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $analyseSols = $em->getRepository(AnalyseSol::class)
            ->getAll();

        return $this->render('Default/analyse_sols.html.twig', array(
            'analyse_sols' => $analyseSols,
        ));
    }

    #[Route(path: '/analyse_sol/{analyse_sol_id}', name: 'analyse_sol')]
    public function produitEditAction($analyse_sol_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        if($analyse_sol_id == '0'){
            $analyse_sol = new AnalyseSol();
        } else {
            $analyse_sol = $em->getRepository(AnalyseSol::class)->findOneById($analyse_sol_id);
        }
        $parcelles =  $em->getRepository(Parcelle::class)->getAllForCampagne($campagne);
        $form = $this->createForm(AnalyseSolType::class, $analyse_sol, array(
            'parcelles' => $parcelles
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $analyse_sol->campagne = $campagne;
            if($analyse_sol->doc){
                if($analyse_sol->doc->getDocFile() || $analyse_sol->doc->getDocName()){
                    $analyse_sol->doc->updatedAt = new Datetime();
                    $analyse_sol->doc->directory = $em->getRepository(DocumentDirectory::class)->findOneByName("analyse_sol");
                    $analyse_sol->doc->date = $analyse_sol->date;
                    $str = $this->stringlify($analyse_sol->parcelle);
                    //$analyse_sol->date->format('Ym')."_"
                    $analyse_sol->doc->name = $str;
                    $em->persist($analyse_sol->doc);
                }
                if($analyse_sol->doc->getDocName() == null){
                    $em->remove($analyse_sol->doc);
                    $analyse_sol->doc = null;
                }
            }
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
