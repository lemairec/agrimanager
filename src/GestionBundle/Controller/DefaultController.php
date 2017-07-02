<?php

namespace GestionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AgriBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;

use DateTime;
use GestionBundle\Entity\Compte;
use GestionBundle\Entity\Ecriture;
use GestionBundle\Entity\Operation;
use GestionBundle\Form\CompteType;
use GestionBundle\Form\EcritureType;
use GestionBundle\Form\OperationType;

//COMPTE
//ECRITURE
//OPERATION


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

    /**
     * @Route("/comptes", name="comptes")
     */
    public function comptesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $comptes = $em->getRepository('GestionBundle:Compte')->getAllForCampagne($campagne);

        return $this->render('GestionBundle:Default:comptes.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'comptes' => $comptes,
        ));
    }

    /**
     * @Route("/compte/{compte_id}", name="compte")
     **/
    public function ilotEditAction($compte_id, Request $request)
    {
        $this->check_user();
        $em = $this->getDoctrine()->getManager();
        $operations = [];
        if($compte_id == '0'){
            $compte = new Compte();
            $compte->company = $this->company;
        } else {
            $compte = $em->getRepository('GestionBundle:Compte')->findOneById($compte_id);
            $operations = $em->getRepository('GestionBundle:Operation')->getAllForCompte($compte);
        }
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($compte);
            $em->flush();
            return $this->redirectToRoute('comptes');
        }
        return $this->render('GestionBundle:Default:compte.html.twig', array(
            'form' => $form->createView(),
            'compte' => $compte,
            'operations' => $operations
        ));
    }

    /**
     * @Route("/operations", name="operations")
     */
    public function operationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $operations = $em->getRepository('GestionBundle:Operation')->findAll();

        return $this->render('GestionBundle:Default:operations.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'operations' => $operations,
        ));
    }

    /**
     * @Route("/operation/{operation_id}", name="operation")
     **/
    public function operationEditAction($operation_id, Request $request)
    {
        $this->check_user();
        $em = $this->getDoctrine()->getManager();
        if($operation_id == '0'){
            $operation = new Operation();
            $operation->name = "new";
            $operation->date = new Datetime();
            $em->persist($operation);
            $em->flush();
        } else {
            $operation = $em->getRepository('GestionBundle:Operation')->findOneById($operation_id);
        }
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('operations');
        }
        return $this->render('GestionBundle:Default:operation.html.twig', array(
            'form' => $form->createView(),
            'operation' => $operation,
            'parcelles' => []
        ));
    }

    /**
     * @Route("/operation/{operation_id}/ecriture/{ecriture_id}", name="ecriture")
     **/
    public function ecritureEditAction($operation_id, $ecriture_id, Request $request)
    {
        $this->check_user();
        $em = $this->getDoctrine()->getManager();
        if($ecriture_id == '0'){
            $ecriture = new Ecriture();
            $ecriture->operation = $em->getRepository('GestionBundle:Operation')->findOneById($operation_id);;
        } else {
            $ecriture = $em->getRepository('GestionBundle:Ecriture')->findOneById($ecriture_id);
        }
        $form = $this->createForm(EcritureType::class, $ecriture);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($ecriture);
            $em->flush();
            return $this->redirectToRoute('operation', array('operation_id' => $operation_id));
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
            'parcelles' => []
        ));
    }
}
