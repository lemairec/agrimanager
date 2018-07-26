<?php

namespace GestionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AgriBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use GestionBundle\Entity\Commercialisation;
use GestionBundle\Form\CommercialisationType;
use Symfony\Component\HttpFoundation\File\File;

//COMPTE
//ECRITURE
//OPERATION


class CommercialisationController extends CommonController
{

    /**
     * @Route("/commercialisations", name="commercialisations")
     */
    public function commercialisationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $commercialisations = $em->getRepository('GestionBundle:Commercialisation')->getAllForCampagne($campagne);

        $cultures = [];
        foreach($commercialisations as $commercialisation){
            if (!array_key_exists(strval($commercialisation->culture), $cultures)) {
                $cultures[strval($commercialisation->culture)] = ['qty' => 0, 'price_total' => 0, "price" => 0];
            }
            $cultures[strval($commercialisation->culture)]['price_total'] += $commercialisation->price_total;
            if($commercialisation->type != "complement"){
                $cultures[strval($commercialisation->culture)]['qty'] += $commercialisation->qty;

            }
            if($cultures[strval($commercialisation->culture)]['qty']>0){
                $cultures[strval($commercialisation->culture)]['price'] = $cultures[strval($commercialisation->culture)]['price_total']/$cultures[strval($commercialisation->culture)]['qty'];
            }
        }

        return $this->render('GestionBundle:Default:commercialisations.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'commercialisations' => $commercialisations,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/commercialisation/{commercialisation_id}", name="commercialisation")
     **/
    public function commercialisationEditAction($commercialisation_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($commercialisation_id == '0'){
            $commercialisation = new Commercialisation();
            $commercialisation->campagne = $campagne;
            $commercialisation->date = new Datetime();
        } else {
            $commercialisation = $em->getRepository('GestionBundle:Commercialisation')->find($commercialisation_id);
        }
        $cultures = $em->getRepository('AgriBundle:Culture')->getAllforCompany($this->company);
        $form = $this->createForm(CommercialisationType::class, $commercialisation, array(
            'cultures' => $cultures
        ));
        $form->handleRequest($request);
        if($commercialisation->qty>0){
            $commercialisation->price = $commercialisation->price_total/$commercialisation->qty;
        }

        if ($form->isSubmitted()) {
            $em->persist($commercialisation);
            $em->flush();
            return $this->redirectToRoute('commercialisations');
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
