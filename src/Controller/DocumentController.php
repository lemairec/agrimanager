<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Document;
use App\Entity\AnalyseSol;
use App\Entity\AppartementOperation;
use App\Form\DocumentType;
use App\Form\DocumentDirectory;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DocumentController extends CommonController
{
    /**
     * @Route("/documents", name="documents")
     */
    public function produitsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $res = $em->getRepository(Document::class)
            ->getAllForCompany($this->company);

        $documents = [];
        foreach($res as $d){
            $d->url = $this->generateUrl('document', array('document_id' => $d->id));
            if($d->directory->name == "analyse_sol"){
                $analyse_sol = $em->getRepository(AnalyseSol::class)
                    ->findOneByDoc($d);
                if($analyse_sol){
                    $d->url = $this->generateUrl('analyse_sol', array('analyse_sol_id' => $analyse_sol->id));
                }
            }
            if($d->directory->name == "appartement"){
                $appartement = $em->getRepository(AppartementOperation::class)
                    ->findOneByDoc($d);
                if($appartement){
                    $d->url = $this->generateUrl('appartement_operation', array('operation_id' => $appartement->id));
                }
            }
            $documents[] = $d;
        }


        return $this->render('Default/documents.html.twig', array(
            'documents' => $documents,
        ));
    }

    /**
     * @Route("/documents2", name="documents2")
     */
    public function documents2Action(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $res = $em->getRepository(Document::class)
            ->getAllOrderDate();

        $directories = [];
        foreach($res as $d){
            $dir = $d->directory->name;
            if(!in_array($dir, $directories)){
                $directories[] = $dir;
            }
        }

        $documentbymonths = [];
        $documents = [];
        foreach($res as $d){
            $months = $d->date->format("Y-m");
            if(!key_exists($months, $documentbymonths)){
                $documentbymonths[$months] = ["mounth"=> $months, "documents" =>[]];
                foreach($directories as $d2){
                    $documentbymonths[$months]["documents"][$d2] = [];
                }
            }

            $d->url = $this->generateUrl('document', array('document_id' => $d->id));
            if($d->directory->name == "analyse_sol"){
                $analyse_sol = $em->getRepository(AnalyseSol::class)
                    ->findOneByDoc($d);
                if($analyse_sol){
                    $d->url = $this->generateUrl('analyse_sol', array('analyse_sol_id' => $analyse_sol->id));
                }
            }
            if($d->directory->name == "appartement"){
                $appartement = $em->getRepository(AppartementOperation::class)
                    ->findOneByDoc($d);
                if($appartement){
                    $d->url = $this->generateUrl('appartement_operation', array('operation_id' => $appartement->id));
                }
            }
            $documentbymonths[$months]["documents"][$d->directory->name][] = $d;
        }

        dump($documentbymonths);


        return $this->render('Default/documents2.html.twig', array(
            'documentbymonths' => $documentbymonths,
            'directory' => $directories,
        ));
    }

    /**
     * @Route("/document/{document_id}", name="document")
     **/
    public function documentEditAction($document_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        if($document_id == '0'){
            $document = new Document();
            $document->company = $this->company;
        } else {
            $document = $em->getRepository(Document::class)->findOneById($document_id);
        }
        $directories = $em->getRepository(DocumentDirectory::class)->getAllForCompany($this->company);
        $form = $this->createForm(DocumentType::class, $document, array(
            'directories' => $directories
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($document);
            $em->flush();
            return $this->redirectToRoute('documents');
        }
        return $this->render('Default/document.html.twig', array(
            'form' => $form->createView(),
            'document' => $document,
        ));
    }

}
