<?php

namespace App\Controller\Lemca;

use Datetime;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Controller\CommonController;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Lemca\Licence;

use App\Form\Lemca\LicenceType;

class LicenceController extends CommonController
{

    #[Route(path: '/lemca/licences', name: 'licences')]
    public function achatsAction(Request $request)
    {
        $c = $this->getCurrentCampagne($request);

        $em = $this->getDoctrine()->getManager();
        $licences = $em->getRepository(Licence::class)->getAll();

        return $this->render('Lemca/licences.html.twig', array(
            'licences' => $licences
        ));
    }

    #[Route(path: '/lemca/licence/{licence_id}', name: 'licence')]
    public function achatEditAction($licence_id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_LEMCA');

        $em = $this->getDoctrine()->getManager();
        if($licence_id == '0'){
            $licence = new Licence();
            $licence->date_create = new \DateTime();
        } else {
            $licence = $em->getRepository(Licence::class)->findOneById($licence_id);
        }

        $form = $this->createForm(LicenceType::class, $licence);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->getRepository(Licence::class)->save($licence);;
            return $this->redirectToRoute('licences');
        }
        return $this->render('Lemca/licence.html.twig', array(
            'form' => $form->createView(),
            'licence' => $licence
        ));
    }


}
