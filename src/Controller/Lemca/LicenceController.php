<?php

namespace App\Controller\Lemca;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Lemca\Licence;



use App\Form\Lemca\LicenceType;

class LicenceController extends AbstractController
{
    /**
     * @Route("/lemca/licences", name = "licences")
     */
    public function achatsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $licences = $em->getRepository('App:Lemca\Licence')->findAll();

        return $this->render('Lemca/licences.html.twig', array(
            'licences' => $licences
        ));
    }

    /**
     * @Route("/lemca/licence/{licence_id}", name="licence")
     **/
    public function achatEditAction($licence_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($licence_id == '0'){
            $licence = new Licence();
        } else {
            $licence = $em->getRepository('App:Lemca\Licence')->findOneById($licence_id);
        }

        $form = $this->createForm(LicenceType::class, $licence);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->getRepository('App:Lemca\Licence')->save($licence);;
            return $this->redirectToRoute('licences');
        }
        return $this->render('Lemca/licence.html.twig', array(
            'form' => $form->createView(),
            'licence' => $licence
        ));
    }
}
