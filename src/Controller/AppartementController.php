<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use App\Entity\AppartementOperation;
use App\Form\AppartementOperationType;
use Symfony\Component\HttpFoundation\File\File;

//COMPTE
//ECRITURE
//OPERATION


class AppartementController extends CommonController
{
    /**
     * @Route("/appartement/operations", name="appartement_operations")
     */
    public function operationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $operations = $em->getRepository("App:AppartementOperation")->getAll();

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

    /**
     * @Route("/appartement/operation/{operation_id}", name="appartement_operation")
     **/
    public function compteEditAction($operation_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($operation_id == '0'){
            $operation = new AppartementOperation();
        } else {
            $operation = $em->getRepository('App:AppartementOperation')->find($operation_id);
        }
        $form = $this->createForm(AppartementOperationType::class, $operation);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('appartement_operations');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/appartement/bilan", name="appartement_bilan")
     */
    public function bilanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $operations = $em->getRepository("App:AppartementOperation")->findAll();


        return $this->render('Default/appartement_operations.html.twig', array(
            'operations' => $operations
        ));
    }
}