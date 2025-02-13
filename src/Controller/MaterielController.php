<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Materiel\Materiel;
use App\Entity\Materiel\MaterielEntretien;

use App\Form\Materiel\MaterielType;
use App\Form\Materiel\MaterielEntretienType;

use Dompdf\Dompdf;
use Dompdf\Options;

class MaterielController extends CommonController
{
    #[Route(path: '/materiels', name: 'materiels')]
    public function materielsAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $materiels = $em->getRepository(Materiel::class)->getAllOkForCompany($this->company);
        foreach($materiels as $m){
            $last = $em->getRepository(MaterielEntretien::class)->getLastEntretiens($m);
            $m->last_entretien = "";
            if($last){
                $m->last_entretien = $last->date->format('d/m/Y')." - ".$last->nbHeure." ".$m->getUnity()." - ".$last->name;
            }
            $last = $em->getRepository(MaterielEntretien::class)->getLastInventaire($m);
            $m->last_inventaire = "";
            if($last){
                $m->last_inventaire = $last->date->format('d/m/Y')." - ".$last->nbHeure." ".$m->getUnity();
            }
        }

        $materiels_ko = $em->getRepository(Materiel::class)->getAllKoForCompany($this->company);
        foreach($materiels_ko as $m){
            $last = $em->getRepository(MaterielEntretien::class)->getLastEntretiens($m);
            $m->last_entretien = "";
            if($last){
                $m->last_entretien = $last->date->format('d/m/Y')." - ".$last->nbHeure." ".$m->getUnity()." - ".$last->name;
            }
            $last = $em->getRepository(MaterielEntretien::class)->getLastInventaire($m);
            $m->last_inventaire = "";
            if($last){
                $m->last_inventaire = $last->date->format('d/m/Y')." - ".$last->nbHeure." ".$m->getUnity();
            }
        }
        
        return $this->render('Materiel/materiels.html.twig', array(
            'materiels' => $materiels,
            'materiels_ko' => $materiels_ko,
            'navs' => ["Materiels" => "materiels"]
        ));
    }

    #[Route(path: '/materiel/{materiel_id}', name: 'materiel')]
    public function materielEditAction($materiel_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $entretiens = [];
        if($materiel_id == '0'){
            $materiel = new Materiel();
            $materiel->company = $this->company;
        } else {
            $materiel = $em->getRepository(Materiel::class)->findOneById($materiel_id);
            $entretiens =  $em->getRepository(MaterielEntretien::class)->getAllByMateriel($materiel);
        }
        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($materiel);
            $em->flush();
            return $this->redirectToRoute('materiels');
        }
        return $this->render('Materiel/materiel.html.twig', array(
            'form' => $form->createView(),
            'materiel' => $materiel,
            'entretiens' => $entretiens,
            'navs' => ["Materiels" => "materiels"]
        ));
    }

    #[Route(path: '/materiel/{materiel_id}/entretien/{entretien_id}', name: 'entretien_materiel')]
    public function entretienMaterielAction($materiel_id, $entretien_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        if($entretien_id == '0'){
            $entretien = new MaterielEntretien();
            $entretien->company = $this->company;
            $entretien->materiel = $em->getRepository(Materiel::class)->findOneById($materiel_id);
            $entretien->date = new \Datetime();
        } else {
            $entretien = $em->getRepository(MaterielEntretien::class)->findOneById($entretien_id);
        }
        $form = $this->createForm(MaterielEntretienType::class, $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($entretien);
            $em->flush();
            return $this->redirectToRoute('materiel', array('materiel_id' => $materiel_id));
        }
        return $this->render('Materiel/materiel_entretien.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    #[Route(path: '/materiels_pdf', name: 'materiels_pdf')]
    public function ficheParcellairesPdf1Action(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $materiels = $em->getRepository(Materiel::class)->getAllOkForCompany($this->company);
        foreach($materiels as $m){
            $m->entretiens = $em->getRepository(MaterielEntretien::class)->getAllByMateriel($m);
        }
        
        $visibility = "visibility: hidden;";
        if($this->getUser()->getUsername() =="lejard"){
            $visibility = "";
        }
        $html = $this->renderView('Materiel/materiels_pdf.html.twig', array(
            'materiels' => $materiels,
            'navs' => ["Materiels" => "materiels"],
            'visibility'=> $visibility

        ));

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        return new Response (
            $dompdf->stream('resume', ["Attachment" => true]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
        return new Response("ok");
    }
}

