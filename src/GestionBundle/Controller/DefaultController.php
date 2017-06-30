<?php

namespace GestionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AgriBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;

use DateTime;
use GestionBundle\Entity\Compte;
use GestionBundle\Form\CompteType;

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
        if($compte_id == '0'){
            $compte = new Compte();
            $compte->company = $this->company;
        } else {
            $compte = $em->getRepository('GestionBundle:Compte')->findOneById($compte_id);
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
            'parcelles' => []
        ));
    }
}
