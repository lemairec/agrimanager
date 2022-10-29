<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Achat;



use App\Form\AchatType;
use App\Form\DataType;


class AlerteController extends CommonController
{
    /**
     * @Route("/alertes", name="alertes")
     */
    public function alertes(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $alerteRepository = $em->getRepository(Alerte::class);
        $alerteRepository->removeAlerteCampagne($campagne);
        $alerteRepository->verifyCampagne($campagne);


        $alertes = $em->getRepository(Alerte::class)->findByCampagne($campagne);
        return $this->render('Default/alertes.html.twig', array(
            'alertes' => $alertes,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'campagne' => $campagne,
            'navs' => ["historique" => "profile_historique"]
        ));
    }



    /**
     * @Route("/run_alertes", name="run_alertes")
     */
    public function run_alertes(Request $request)
    {
        return $this->redirect("alertes");
    }
}
