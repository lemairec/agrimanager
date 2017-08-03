<?php

namespace GestionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AgriBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;

use DateTime;
use GestionBundle\Entity\Compte;
use GestionBundle\Entity\Ecriture;
use GestionBundle\Entity\Operation;
use GestionBundle\Entity\FactureFournisseur;
use GestionBundle\Form\CompteType;
use GestionBundle\Form\EcritureType;
use GestionBundle\Form\OperationType;
use GestionBundle\Form\FactureFournisseurType;

//COMPTE
//ECRITURE
//OPERATION


class DefaultController extends CommonController
{
    /**
     * @Route("/cours", name="cours")
     */
    public function coursAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $courss = $em->getRepository('GestionBundle:Cours')->getAllForCampagne($campagne);

        return $this->render('GestionBundle:Default:cours.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'courss' => $courss,
        ));
    }

    /**
     * @Route("/cours/new", name="cours_new")
     */
    public function coursNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->check_user($request);

        $courss = [['name'=>'2017_ble', 'value'=>150], ['name'=>'2018_ble', 'value'=>150]
            ,['name'=>'2017_colza', 'value'=>350], ['name'=>'2018_colza', 'value'=>350]
            ,['name'=>'2017_orge', 'value'=>170], ['name'=>'2018_orge', 'value'=>170]];
        if ($request->getMethod() == 'POST') {
            $em->getRepository('GestionBundle:Cours')->saveArray($this->company, $request->request->all());
            return $this->redirectToRoute('cours');
        }
        $date = new DateTime();
        return $this->render('GestionBundle:Default:cours_new.html.twig', array(
            'date' => $date->format("d/m/Y"),
            'courss' => $courss,
        ));
    }

    /**
     * @Route("/comptes", name="comptes")
     */
    public function comptesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $comptes = $em->getRepository('GestionBundle:Compte')->getAllForCampagne($campagne);

        return $this->render('GestionBundle:Default:comptes.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'comptes' => $comptes,
        ));
    }

    /**
     * @Route("/banque", name="banque")
     **/
    public function banqueEditAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $banque = $em->getRepository('GestionBundle:Compte')->getFirstBanque();
        return $this->redirectToRoute('compte', array('compte_id' => $banque->id));
    }


    /**
     * @Route("/compte/{compte_id}", name="compte")
     **/
    public function compteEditAction($compte_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $operations = [];
        if($compte_id == '0'){
            $compte = new Compte();
            $compte->company = $this->company;
        } else {
            $compte = $em->getRepository('GestionBundle:Compte')->findOneById($compte_id);
            $operations = $em->getRepository('GestionBundle:Operation')->getAllForCompte($compte);
            $ecritures = [];
            $value = 0;
            $l = count($operations);
            for($i = 0; $i < $l; ++$i){
                $operation = $operations[$i];
                foreach($operation->ecritures as $e){
                    if($e->compte == $compte){
                    $ecriture = ['operation_id'=>$operation->id,'date'=>$operation->getDateStr(), 'name'=>$operation->name, 'value'=>$e->value];
                    $ecriture['campagne'] = "";
                    if($e->campagne){
                        $ecriture['campagne'] = $e->campagne->name;
                    }
                    if($compte->type == 'banque'){
                        $ecriture['value'] = -$ecriture['value'];
                    }

                    $value += $ecriture['value'];
                    $ecriture['sum_value'] = $value;

                    $ecritures[] = $ecriture;
                }
                }

            }
            $ecritures = array_reverse($ecritures);
        }
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($compte);
            $em->flush();
            return $this->redirectToRoute('comptes');
        }
        return $this->render('GestionBundle:Default:compte.html.twig', array(
            'form' => $form->createView(),
            'compte' => $compte,
            'ecritures' => $ecritures
        ));
    }

    /**
     * @Route("/operations", name="operations")
     */
    public function operationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $operations = $em->getRepository('GestionBundle:Operation')->getAll();

        return $this->render('GestionBundle:Default:operations.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'operations' => $operations,
        ));
    }

    /**
     * @Route("/operation/{operation_id}", name="operation")
     **/
    public function operationEditAction($operation_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        if($operation_id == '0'){
            $operation = new Operation();
            $operation->name = "new";
            $operation->date = new Datetime();
            $operation->ecritures = [];
            $em->persist($operation);
            $em->flush();
        } else {
            $operation = $em->getRepository('GestionBundle:Operation')->findOneById($operation_id);
            if($operation->facture){
                return $this->redirectToRoute('facture_fournisseur', array('facture_id' => $operation->facture->id));
            }
        }
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('operations');
        }
        return $this->render('GestionBundle:Default:operation.html.twig', array(
            'form' => $form->createView(),
            'operation' => $operation,
            'parcelles' => []
        ));
    }

    /**
     * @Route("/operation/{operation_id}/delete", name="operation_delete")
     **/
    public function operationDeleteAction($operation_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('GestionBundle:Operation')->delete($operation_id);
        return $this->redirectToRoute('operations');
    }

    /**
     * @Route("/operation/{operation_id}/ecriture/{ecriture_id}", name="ecriture")
     **/
    public function ecritureEditAction($operation_id, $ecriture_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        if($ecriture_id == '0'){
            $ecriture = new Ecriture();
            $ecriture->operation = $em->getRepository('GestionBundle:Operation')->findOneById($operation_id);;
        } else {
            $ecriture = $em->getRepository('GestionBundle:Ecriture')->findOneById($ecriture_id);
        }
        $campagnes = $em->getRepository('AgriBundle:Campagne')->getAllAndNullforCompany($this->company);
        $form = $this->createForm(EcritureType::class, $ecriture, array(
            'comptes' => $em->getRepository('GestionBundle:Compte')->getAll(),
            'campagnes' => $campagnes
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($ecriture);
            $em->flush();
            return $this->redirectToRoute('operation', array('operation_id' => $operation_id));
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/facture_fournisseurs", name="factures_fournisseurs")
     **/
    public function factureFournisseursAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $facture_fournisseurs = $em->getRepository('GestionBundle:FactureFournisseur')->getAll();

        return $this->render('GestionBundle:Default:facture_fournisseurs.html.twig', array(
            'facture_fournisseurs' => $facture_fournisseurs
        ));
    }

    /**
     * @Route("/facture_fournisseur/{facture_id}", name="facture_fournisseur")
     **/
    public function factureFournisseurAction($facture_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $operations = [];
        if($facture_id == '0'){
            $facture = new FactureFournisseur();
            $facture->date = new Datetime();
        } else {
            $facture = $em->getRepository('GestionBundle:FactureFournisseur')->findOneById($facture_id);
            $operations = $em->getRepository('GestionBundle:Operation')->getForFacture($facture);
        }
        $campagnes = $em->getRepository('AgriBundle:Campagne')->getAllAndNullforCompany($this->company);
        $banques = $em->getRepository('GestionBundle:Compte')->getAllBanques($this->company);
        $comptes = $em->getRepository('GestionBundle:Compte')->getNoBanques($this->company);
        $form = $this->createForm(FactureFournisseurType::class, $facture, array(
            'banques' => $banques,
            'comptes' => $comptes,
            'campagnes' => $campagnes
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->getRepository('GestionBundle:FactureFournisseur')->save($facture);
            return $this->redirectToRoute('factures_fournisseurs');
        }
        return $this->render('GestionBundle:Default:facture_fournisseur.html.twig', array(
            'form' => $form->createView(),
            'facture' => $facture,
            'operations' => $operations
        ));
    }

    /**
     * @Route("/facture_fournisseur/{facture_id}/delete", name="facture_fournisseur_delete")
     **/
    public function factureFournisseurDeleteAction($facture_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('GestionBundle:FactureFournisseur')->delete($facture_id);
        return $this->redirectToRoute('factures_fournisseurs');
    }
}
