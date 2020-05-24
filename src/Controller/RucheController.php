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
            ->getAll();
        $ruches = $em->getRepository('App:Ruche\Ruche')
                ->findByRucher(null);
        $actions = $em->getRepository('App:Ruche\Action')
                ->getAll();

        $sum = 0;
        foreach($ruchers as $rucher){
            $sum += $rucher->ruchesCount();
        }
        
        return $this->render('Ruche/apiculture.html.twig', array(
            'ruchers' => $ruchers,
            'ruches' => $ruches,
            'actions' => $actions,
            'sum_ruches' => $sum
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

        $essaims = $em->getRepository('App:Ruche\Essaim')->findAll($id);
        $essaims[] = null;
        $ruches = $em->getRepository('App:Ruche\Ruche')->findAll($id);
        $ruches[] = null;
        $ruchers = $em->getRepository('App:Ruche\Rucher')->findAll($id);
        $ruchers[] = null;
        $form = $this->createForm(ActionType::class, $action, array(
            'essaims' => $essaims,
            'ruches' => $ruches,
            'ruchers' => $ruchers
        ));
        $form->handleRequest($request);



        if ($form->isSubmitted()) {
            if($action->type == "Enruchage"){
                $ruches = $em->getRepository('App:Ruche\Ruche')->findByEssaim($action->essaim);
                foreach($ruches as $ruche){
                    $ruche->essaim = null;
                    $ruche->rucher = null;
                    $em->persist($ruche);
                    $em->flush();
                }
                $action->ruche->essaim = $action->essaim;
                $action->ruche->rucher = $action->rucher;
                $em->persist($action->ruche);
                $em->flush();
            } else if($action->type == "Mort"){
                $action->ruche->essaim->visible = false;
                $action->essaim = $action->ruche->essaim;
                $action->rucher= $action->ruche->rucher;;
                $em->persist($action->ruche->essaim);
                $action->ruche->essaim = null;
                $action->ruche->rucher = null;
                $em->persist($action->ruche);
                $em->flush();
            } else {
                $action->essaim = $action->ruche->essaim;
                $action->rucher= $action->ruche->rucher;;

            }
            $em->persist($action);
            $em->flush();
            return $this->redirectToRoute('apiculture');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
