<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use App\Entity\AppartementOperation;
use App\Entity\DocumentDirectory;
use App\Form\AppartementOperationType;
use Symfony\Component\HttpFoundation\File\File;

//COMPTE
//ECRITURE
//OPERATION


class AppartementController extends CommonController
{
    #[Route(path: '/appartement/operations', name: 'appartement_operations')]
    public function operationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $operations = $em->getRepository(AppartementOperation::class)->getAll();

        $sum = 0;
        $l = count($operations);
        for($i = 0; $i < $l; ++$i){
            $o = $operations[$l-1-$i];
            $sum += $o->value;
            $o->sum = $sum;
        }

        return $this->render('Default/appartement_operations.html.twig', array(
            'operations' => $operations
        ));
    }

    #[Route(path: '/appartement/operation/{operation_id}', name: 'appartement_operation')]
    public function compteEditAction($operation_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        if($operation_id == '0'){
            $operation = new AppartementOperation();
            $operation->date = new \DateTime();
        } else {
            $operation = $em->getRepository(AppartementOperation::class)->find($operation_id);
        }
        $form = $this->createForm(AppartementOperationType::class, $operation);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            if($operation->doc){
                if($operation->doc->getDocFile() || $operation->doc->getDocName()){
                    $operation->doc->updatedAt = new Datetime();
                    $operation->doc->repository = "appartement";
                    $operation->doc->directory = $em->getRepository(DocumentDirectory::class)->findOneByName("appartement");
                    $operation->doc->company = $this->company;
                    $operation->doc->date = $operation->date;
                    $str = $this->stringlify($operation->type);
                    $operation->doc->name = $str;
                    $em->persist($operation->doc);
                }
                if($operation->doc->getDocName() == null){
                    $em->remove($operation->doc);
                    $operation->doc = null;
                }
            }
            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('appartement_operations');
        }
        return $this->render('Default/appartement_operation.html.twig', array(
            'form' => $form->createView(),
            'operation' => $operation
        ));
    }

    #[Route(path: '/appartement/bilan', name: 'appartement_bilan')]
    public function bilanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $operations = $em->getRepository(AppartementOperation::class)->findAll();


        return $this->render('Default/appartement_operations.html.twig', array(
            'operations' => $operations
        ));
    }
}
