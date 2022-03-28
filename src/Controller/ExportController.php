<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Document;
use App\Form\DocumentType;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ExportController extends CommonController
{
    /**
     * @Route("/export", name="export")
     **/
    public function export(Request $request)
    {
        return $this->render('Export/export.html.twig');
    }

    /**
     * @Route("/export_preview/year/{year}/month/{month}", name="export_preview")
     **/
    public function export_preview(Request $request, $month, $year)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        
        $date_begin = new Datetime("$year-$month-01");
        $date_end = new Datetime("$year-$month-01");
        $date_end = $date_end->modify('+1 month');
        
        $factures = $em->getRepository('App:Gestion\FactureFournisseur')->getAllForExport2($this->company, $date_begin, $date_end);

        $documents = $em->getRepository('App:Document')->getAllForExport2($this->company, $date_begin, $date_end);

        return $this->render('Export/export_preview.html.twig', [
            'date_begin' => $date_begin,
            'date_end' => $date_end,
            'month' => $month,
            'year' => $year,
            'factures' => $factures,
            'documents' => $documents
        ]);
    }

     /**
     * @Route("/export_2/year/{year}/month/{month}", name="export3")
     **/
    public function export_3(Request $request, $month, $year)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        
        $date_begin = new Datetime("$year-$month-01");
        $date_end = new Datetime("$year-$month-01");
        $date_end = $date_end->modify('+1 month');
        
        $factures = $em->getRepository('App:Gestion\FactureFournisseur')->getAllForExport2($this->company, $date_begin, $date_end);

        $documents = $em->getRepository('App:Document')->getAllForExport2($this->company, $date_begin, $date_end);

        $zip = new \ZipArchive();
        $zipName = 'Documents_'.$this->company->name.'_'.$year.'_'.$month.".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);
        
        foreach ($factures as $f) {
            $file = $f->getFactureFileName();
            if($file){
                $fileName = $f->getFactureMyFileName();
                $src = "uploads/factures/".$file;
                $zip->addFile($src, "facture/".$fileName);
                $f->dateExport = new DateTime();
                $em->persist($f);
            }
        }
        
        foreach ($documents as $f) {
            $file = $f->getDocName();
            if($file){
                $fileName = $f->getDocMyFileName();
                $src = "uploads/documents/".$file;
                $zip->addFile($src,  $f->directory->name."/".$fileName);
                $f->dateExport = new DateTime();
                $em->persist($f);
            }
        }
        $em->flush();
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }

    /**
     * @Route("/documents/export", name="document_export")
     **/
    public function documentsDump(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($this->getUser()->getUsername() != "lejard"){
            return new Response("not authorize");
        }


        $zip = new \ZipArchive();
        $zipName = 'documents_'.time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);

        foreach ($em->getRepository('App:Document')->findAll() as $f) {
            $file = $f->getDocName();
            if($file){
                $fileName = $f->getDocMyFileName();
                $src = "uploads/documents/".$file;
                $zip->addFile($src,  $f->directory->name."/".$fileName);
                $f->dateExport = new DateTime();
                $em->persist($f);
            }
        }
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }


    /**
     * @Route("export_update/company", name="export_update_company")
     **/
    public function factureFournisseurExportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
       
        $zip = new \ZipArchive();
        $zipName = 'Documents_'.time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);
        
        foreach ($em->getRepository('App:Gestion\FactureFournisseur')->getAllForExport($this->company) as $f) {
            $file = $f->getFactureFileName();
            if($file){
                $fileName = $f->getFactureMyFileName();
                $src = "uploads/factures/".$file;
                $zip->addFile($src, "facture/".$fileName);
                $f->dateExport = new DateTime();
                $em->persist($f);
            }
        }
        
        foreach ($em->getRepository('App:Document')->getAllForExport($this->company) as $f) {
            $file = $f->getDocName();
            if($file){
                $fileName = $f->getDocMyFileName();
                $src = "uploads/documents/".$file;
                $zip->addFile($src,  $f->directory->name."/".$fileName);
                $f->dateExport = new DateTime();
                $em->persist($f);
            }
        }
        $em->flush();
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }
    

    /**
     * @Route("export/company", name="export_company")
     **/
    public function exportCOmpanyAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
       
        $zip = new \ZipArchive();
        $zipName = 'Documents_'.time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);
        
        foreach ($em->getRepository('App:Gestion\FactureFournisseur')->findByCompany($this->company) as $f) {
            $file = $f->getFactureFileName();
            if($file){
                $fileName = $f->getFactureMyFileName();
                $src = "uploads/factures/".$file;
                $zip->addFile($src, "facture/".$fileName);
            }
        }
        
        foreach ($em->getRepository('App:Document')->findByCompany($this->company) as $f) {
            $file = $f->getDocName();
            if($file){
                $fileName = $f->getDocMyFileName();
                $src = "uploads/documents/".$file;
                $zip->addFile($src,  $f->directory->name."/".$fileName);

            }
        }
        $em->flush();
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }

    /**
     * @Route("export/company_date", name="export_company")
     **/
    public function exportCompanyDateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
       
        $zip = new \ZipArchive();
        $zipName = 'Documents_'.time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);
        
        foreach ($em->getRepository('App:Gestion\FactureFournisseur')->findByCompany($this->company) as $f) {
            $file = $f->getFactureFileName();
            if($file && $f->date->format('y') == "21"){
                $fileName = $f->getFactureMyFileName();
                $src = "uploads/factures/".$file;
                $zip->addFile($src, "facture/".$fileName);
            }
        }
        
        foreach ($em->getRepository('App:Document')->findByCompany($this->company) as $f) {
            $file = $f->getDocName();
            if($file && $f->date->format('y') == "21"){
                $fileName = $f->getDocMyFileName();
                $src = "uploads/documents/".$file;
                $zip->addFile($src,  $f->directory->name."/".$fileName);

            }
        }
        $em->flush();
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }

    /**
     * @Route("/facture_fournisseurs_export", name="factures_fournisseurs2")
     **/
    public function factureFournisseurs2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $facture_fournisseurs = $em->getRepository('App:Gestion\FactureFournisseur')->getAll();

        return $this->render('Gestion/facture_fournisseurs_export.html.twig', array(
            'facture_fournisseurs' => $facture_fournisseurs
        ));
    }


    /**
    * @Route("/export_all", name="export_all")
    **/
    public function exportDump(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($this->getUser()->getUsername() != "lejard"){
            return new Response("not authorize");
        }

        //remove old
        $command = "rm dump/*.zip; rm dump/*.sql";
        $process= new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        //backup
        $backupFile = 'dump/backup'.time().'.sql';
        $host = getenv('DATABASE_HOST');
        $port = getenv('DATABASE_PORT');
        $db = getenv('DATABASE_NAME');
        $user = getenv('DATABASE_USER');
        $password = getenv('DATABASE_PASSWORD');
        $command = "mysqldump -u $user --password=$password --host=$host --port=$port --opt $db > $backupFile";
        $process= new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $zip = new \ZipArchive();
        $zipName = 'dump/'.time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);

        foreach ($em->getRepository('App:Document')->findAll() as $f) {
            $file = $f->getDocName();
            if($file){
                $src = "uploads/documents/".$file;
                $zip->addFile($src, $file);
            }
        }

        $zip->addEmptyDir("factures");
        foreach ($em->getRepository('App:Gestion\FactureFournisseur')->findAll() as $f) {
            $file = $f->getFactureFileName();
            if($file){
                $src = "uploads/factures/".$file;
                $zip->addFile($src, "factures/".$file);
            }
        }
        $zip->addFile($backupFile, "database.sql");
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }
}
