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
            $d->url = $this->generateUrl('document', array('document_id' => $d->id));
            if($d->repository == "analyse_sol"){
                $analyse_sol = $em->getRepository('App:AnalyseSol')
                    ->findOneByDoc($d);
                if($analyse_sol){
                    $d->url = $this->generateUrl('analyse_sol', array('analyse_sol_id' => $analyse_sol->id));
                }
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

    /**
     * @Route("/documents/dump", name="document_dump")
     **/
    public function documentsDump(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($this->getUser()->getEmail() != "lemairec02@gmail.com"){
            return new Response("not authorize");
        }


        $zip = new \ZipArchive();
        $zipName = 'Dump_'.time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);

        foreach ($em->getRepository('App:Document')->findAll() as $f) {
            $file = $f->getDocName();
            if($file){
                $src = "uploads/documents/".$file;
                $zip->addFile($src, $file);
            }
        }
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }
}
