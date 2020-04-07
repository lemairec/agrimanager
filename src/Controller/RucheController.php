<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Ruche\Rucher;
use App\Entity\Ruche\Ruche;
use App\Entity\Ruche\Essaim;
use App\Entity\Ruche\Action;
use App\Form\Ruche\RucherType;
use App\Form\Ruche\RucheType;
use App\Form\Ruche\EssaimType;
use App\Form\Ruche\ActionType;

class RucheController extends CommonController
{
    /**
     * @Route("/apiculture", name = "apiculture")
     */
    public function apicultureAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ruchers = $em->getRepository('App:Ruche\Rucher')
            ->findAll();
        $ruches = $em->getRepository('App:Ruche\Ruche')
                ->findByRucher(null);
        $actions = $em->getRepository('App:Ruche\Action')
                ->getAll();

        return $this->render('Ruche/apiculture.html.twig', array(
            'ruchers' => $ruchers,
            'ruches' => $ruches,
            'actions' => $actions
        ));
    }


    /**
     * @Route("/ruchers", name = "ruchers")
     */
    public function ruchersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ruchers = $em->getRepository('App:Ruche\Rucher')
            ->findAll();

        return $this->render('Ruche/ruchers.html.twig', array(
            'ruchers' => $ruchers
        ));
    }

    /**
     * @Route("/rucher/{id}", name="rucher")
     **/
    public function rucherEditAction($id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        if($id == '0'){
            $rucher = new Rucher();
        } else {
            $rucher = $em->getRepository('App:Ruche\Rucher')->findOneById($id);
        }
        $form = $this->createForm(RucherType::class, $rucher);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($rucher);
            $em->flush();
            return $this->redirectToRoute('ruchers');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/ruches", name = "ruches")
     */
    public function ruchesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ruches = $em->getRepository('App:Ruche\Ruche')
            ->findAll();

        return $this->render('Ruche/ruches.html.twig', array(
            'ruches' => $ruches
        ));
    }

    /**
     * @Route("/ruche/{id}", name="ruche")
     **/
    public function rucheEditAction($id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        if($id == '0'){
            $ruche = new Ruche();
        } else {
            $ruche = $em->getRepository('App:Ruche\Ruche')->findOneById($id);
        }
        $form = $this->createForm(RucheType::class, $ruche);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($ruche);
            $em->flush();
            return $this->redirectToRoute('ruches');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/essaims", name = "essaims")
     */
    public function essaimsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $essaims = $em->getRepository('App:Ruche\Essaim')
            ->findAll();

        return $this->render('Ruche/essaims.html.twig', array(
            'essaims' => $essaims
        ));
    }

    /**
     * @Route("/essaim/{id}", name="essaim")
     **/
    public function essaimEditAction($id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        $actions = [];
        if($id == '0'){
            $essaim = new Essaim();
        } else {
            $essaim = $em->getRepository('App:Ruche\Essaim')->findOneById($id);
            $actions =  $em->getRepository('App:Ruche\Action')->getAllForEssaim($essaim);
        }
        $form = $this->createForm(EssaimType::class, $essaim);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($essaim);
            $em->flush();
            return $this->redirectToRoute('essaims');
        }
        return $this->render('Ruche/essaim.html.twig', array(
            'form' => $form->createView(),
            'actions' => $actions
        ));
    }

    /**
     * @Route("/action/{id}", name="action")
     **/
    public function actionEditAction($id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        if($id == '0'){
            $action = new Action();
            $action->date = new \DateTime();
        } else {
            $action = $em->getRepository('App:Ruche\Action')->find($id);
        }

        $ess = $em->getRepository('App:Ruche\Essaim')->findAll($id);
        $essaims = [];
        foreach($ess as $e){
            $essaims[] = ["id"=>$e->id, "name"=>$e->__toString()];
        }

        $ruchs = $em->getRepository('App:Ruche\Rucher')->findAll($id);
        $ruchers = [];
        foreach($ruchs as $e){
            $ruchers[] = ["id"=>$e->id, "name"=>$e->__toString()];
        }

        $ruchs = $em->getRepository('App:Ruche\Ruche')->findAll($id);
        $ruches = [];
        foreach($ruchs as $e){
            $ruches[] = ["id"=>$e->id, "name"=>$e->__toString()];
        }
        
        return $this->render('Ruche/action.html.twig', array(
            'action' => $action,
            'essaims' => $essaims,
            'ruches' => $ruches,
            'ruchers' => $ruchers,
        ));
    }
}
