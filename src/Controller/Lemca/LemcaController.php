<?php

namespace App\Controller\Lemca;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Lemca\Panel;
use App\Entity\Lemca\Kit;
use App\Entity\Lemca\Camera;



use App\Form\Lemca\PanelType;
use App\Form\Lemca\KitType;
use App\Form\Lemca\CameraType;


class LemcaController extends CommonController
{
    /**
     * @Route("/lemca/panels", name = "panels")
     */
    public function achatsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $panels = $em->getRepository('App:Lemca\Panel')->findAll();

        return $this->render('Lemca/panels.html.twig', array(
            'panels' => $panels
        ));
    }

    /**
     * @Route("/lemca/panel/{panel_id}", name="panel")
     **/
    public function achatEditAction($panel_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($panel_id == '0'){
            $panel = new Panel();
            $panel->date = new \DateTime();
        } else {
            $panel = $em->getRepository('App:Lemca\Panel')->findOneById($panel_id);
        }

        $form = $this->createForm(PanelType::class, $panel);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($panel);
            $em->flush();
            return $this->redirectToRoute('panels');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/lemca/kits", name = "kits")
     */
    public function kitsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $kits = $em->getRepository('App:Lemca\Kit')->findAll();

        return $this->render('Lemca/kits.html.twig', array(
            'kits' => $kits
        ));
    }

    /**
     * @Route("/lemca/kit/{kit_id}", name="kit")
     **/
    public function kitEditAction($kit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($kit_id == '0'){
            $kit = new Kit();
            $kit->date = new \DateTime();
        } else {
            $kit = $em->getRepository('App:Lemca\Kit')->findOneById($kit_id);
        }

        $form = $this->createForm(KitType::class, $kit);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($kit);
            $em->flush();
            return $this->redirectToRoute('kits');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/lemca/cameras", name = "cameras")
     */
    public function camerasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cameras = $em->getRepository('App:Lemca\Camera')->findAll();

        return $this->render('Lemca/cameras.html.twig', array(
            'cameras' => $cameras
        ));
    }

    /**
     * @Route("/lemca/camera/{camera_id}", name="camera")
     **/
    public function cameraEditAction($camera_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($camera_id == '0'){
            $camera = new Camera();
            $camera->date = new \DateTime();
        } else {
            $camera = $em->getRepository('App:Lemca\Camera')->findOneById($camera_id);
        }

        $form = $this->createForm(CameraType::class, $camera);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($camera);
            $em->flush();
            return $this->redirectToRoute('cameras');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
