<?php

namespace App\Controller\Lemca;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Lemca\Branch;


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


        $path = __DIR__."/../../public/lemca";
        $file = $request->files->get('myfile');
        $filename = 'bineuse_'.$branch_name.'.tar.gz';
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

    #[Route(path: '/lemca/bineuse_binary', name: 'get_binary')]
    public function achatEditA2ction(Request $request){
        $branch_name = $request->query->get("branch");
        $branch = $em->getRepository(Branch::class)->findOneByName($branch_name);

        $path = __DIR__."/../../public/lemca/".$branch->filename;
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



}
