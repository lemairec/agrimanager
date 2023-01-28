<?php

namespace App\Controller\Lemca;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Lemca\Branch;
use App\Entity\Lemca\LemcaFile;

class BinaryController extends CommonController
{
    #[Route(path: '/lemca/send_file', name: 'lemca_send_file')]
    public function send_file(Request $request)
    {
        $branch_name = $request->query->get("branch");
        $head = $request->query->get("head");
        $em = $this->getDoctrine()->getManager();
        $branch = $em->getRepository(Branch::class)->findOneByName($branch_name);
        if($branch == NULL){
            $branch = new Branch();
            $branch->name = $branch_name;
        }


        $path = __DIR__."/../../../public/binaries";
        $file = $request->files->get('myfile');
        $filename = "bineuse_".$branch_name.".tar.gz";
        $file->move(
            $path,
            $filename
        );

        $branch->filename = $filename;
        $branch->date = new DateTime();
        $branch->log = $branch->log.$branch->date->format('Y-m-d H:i:s')." ".$head."\n";
        $em->persist($branch);
        $em->flush();

        return new JsonResponse("ok");

    }

    #[Route(path: '/lemca/send_file2', name: 'lemca_send_file2')]
    public function send_file2(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $path = __DIR__."/../../../public/lemca_files";
        $file = $request->files->get('myfile');
        $filename = $file->getClientOriginalName();

        $lemca_file = $em->getRepository(LemcaFile::class)->findOneByFilename($filename);
        if($lemca_file == NULL){
            $lemca_file = new LemcaFile();
        }
        $lemca_file->filename = $filename;
        $lemca_file->datetime = new DateTime();


        $file->move(
            $path,
            $filename
        );

        $em->persist($lemca_file);
        $em->flush();

        return new JsonResponse("ok");

    }

    #[Route(path: '/lemca/bineuse_binary', name: 'get_binary')]
    public function achatEditA2ction(Request $request){
        $branch_name = $request->query->get("branch");

        $em = $this->getDoctrine()->getManager();
        $branch = $em->getRepository(Branch::class)->findOneByName($branch_name);

        $filename = $branch->filename;
        $path = __DIR__."/../../../public/binaries/".$filename;
        return $this->file($path, "bineuse.tar.gz");
    }

    #[Route(path: '/lemca/binaries', name: 'binaries')]
    public function achatBinariesAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $panels = $em->getRepository(Branch::class)->findAll();

        return $this->render('Lemca/binaries.html.twig', array(
            'binaries' => $panels
        ));
    }

    #[Route(path: '/lemca/files', name: 'lemca_files')]
    public function filesAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $panels = $em->getRepository(LemcaFile::class)->findAll();

        return $this->render('Lemca/files.html.twig', array(
            'files' => $panels
        ));
    }



}
