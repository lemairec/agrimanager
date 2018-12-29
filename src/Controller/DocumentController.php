<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Document;
use App\Form\DocumentType;


class DocumentController extends CommonController
{
    /**
     * @Route("/documents", name="documents")
     */
    public function produitsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $res = $em->getRepository('App:Document')
            ->findAll();

        $documents = [];
        foreach($res as $d){
            if($d->repository == "analyse_sol"){
                $analyse_sol = $em->getRepository('App:AnalyseSol')
                    ->findOneByDoc($d);
                $d->url = $this->generateUrl('analyse_sol', array('analyse_sol_id' => $analyse_sol->id));
            } else {
                $d->url = $this->generateUrl('document', array('document_id' => $d->id));
            }
            $documents[] = $d;
        }


        return $this->render('Default/documents.html.twig', array(
            'documents' => $documents,
        ));
    }

    /**
     * @Route("/document/{document_id}", name="document")
     **/
    public function produitEditAction($document_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        if($document_id == '0'){
            $document = new Document();
        } else {
            $document = $em->getRepository('App:Document')->findOneById($document_id);
        }
        $form = $this->createForm(DocumentType::class, $document);
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
