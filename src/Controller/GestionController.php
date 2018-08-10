<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use App\Entity\Compte;
use App\Entity\Ecriture;
use App\Entity\Operation;
use App\Entity\FactureFournisseur;
use App\Form\CompteType;
use App\Form\EcritureType;
use App\Form\OperationType;
use App\Form\FactureFournisseurType;
use Symfony\Component\HttpFoundation\File\File;

//COMPTE
//ECRITURE
//OPERATION


class GestionController extends CommonController
{
    /**
     * @Route("/cours", name="cours")
     */
    public function coursAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $courss = $em->getRepository('App:Cours')->getAllForCampagne($campagne);

        $produits = [];
        foreach($courss as $c){
            $produit = $c->produit;
            $value = $c->value;
            if (!array_key_exists($produit, $produits)) {
                $produits[$produit] = ['name'=>$produit,'last' => $value,  'min' => $value, 'max' => $value];
            }
            $produits[$produit]['min'] = min($produits[$produit]['min'], $value);
            $produits[$produit]['max'] = max($produits[$produit]['max'], $value);
        }
        $courss = $em->getRepository('App:Cours')->getAllForCampagne($campagne);

        return $this->render('Default/cours.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'courss' => $courss,
            'produits' => $produits
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
        $courss = $em->getRepository('App:Cours')->setArray($this->company, $courss);
        if ($request->getMethod() == 'POST') {
            $em->getRepository('App:Cours')->saveArray($this->company, $request->request->all());
            return $this->redirectToRoute('cours');
        }
        $date = new DateTime();
        return $this->render('Default/cours_new.html.twig', array(
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

        $comptes = $em->getRepository('App:Compte')->getAllForCampagne($campagne);

        $comptes_campagnes = [];
        foreach ($this->campagnes as $campagne) {
            $res = 0;
            foreach ($comptes as $compte) {
                if ($compte->type == 'campagne' || $compte->getPriceCampagne($campagne) != 0){
                    $res = $res + $compte->getPriceCampagne($campagne);
                }
            }
            $comptes_campagnes[$campagne->name] = $res;
        }

        return $this->render('Default/comptes.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'comptes' => $comptes,
            'comptes_campagnes' => $comptes_campagnes
        ));
    }

    /**
     * @Route("/banque", name="banque")
     **/
    public function banqueEditAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $banque = $em->getRepository('App:Compte')->getFirstBanque();
        $compte = $banque;
        $operations = $em->getRepository('App:Operation')->getAllForCompte($compte);
        $ecritures = [];
        $value = 0;
        $l = count($operations);
        $year = "";
        $month = "";
        $value_month = [];
        for($i = 0; $i < $l; ++$i){
            $operation = $operations[$i];
            $ignore = false;
            foreach($operation->ecritures as $e){
                if($e->compte->name=="400. Installation"
                || $e->compte->name=="800. reprise"
                || $e->compte->name=="300. Cours Terme"
                || $e->compte->name=="002. hsbc"){
                    $ignore = true;

                }
                if($e->compte->name=="910. emprunt lt" && $e->value > 0){
                    $ignore = true;
                }
            }

            foreach($operation->ecritures as $e){

                if($e->compte == $compte){
                    $new_year = $operation->date->format('Y');
                    $new_month = $operation->date->format('Y-m');
                    if($new_month != $month){
                        $m = $operation->date->format('m');
                        $value_month[] = ['year'=>$year, 'month'=>$m, 'value'=>$value];
                        $month = $new_month;
                    }
                    if($new_year != $year){
                        $ecriture = ['operation_id'=>'annee', 'campagne'=>$year, 'date'=>$year, 'name'=>$year, 'value'=>0, 'ignore'=>false, 'sum_value'=>$value];
                        $ecritures[] = $ecriture;
                        $year = $new_year;
                        $value = 0;
                    }

                    //print($new_year);
                    $ecriture = ['operation_id'=>$operation->id,'date'=>$operation->getDateStr(), 'name'=>$operation->name, 'value'=>$e->value, 'ignore'=>$ignore];
                    $ecriture['campagne'] = "";
                    if($e->campagne){
                        $ecriture['campagne'] = $e->campagne->name;
                    }
                    if($compte->type == 'banque'){
                        $ecriture['value'] = -$ecriture['value'];
                    }

                    if($ignore){

                    } else {
                        $value += $ecriture['value'];

                    }
                    $ecriture['sum_value'] = $value;

                    $ecritures[] = $ecriture;
                }
            }
        }

        $colors = ["" => "", "2016" => "#99ccff", "2017" => "#ff9966"];

        $chartjss = [];
        $chartjs = NULL;
        foreach($ecritures as $ecriture){
            if($ecriture['operation_id'] == 'annee'){
                if($chartjs){
                    $chartjss[] = $chartjs;
                }
                $chartjs = ['annee'=> $ecriture['campagne'], 'data' => [], 'color' => $colors[$ecriture['campagne']]];
                continue;
            }
            $chartjs['data'][] = ['date' => substr($ecriture['date'], 1, 6)."2017", 'value' => $ecriture['sum_value']];
        }
        $chartjss[] = $chartjs;

        //print(json_encode($value_month));
        $ecritures = array_reverse($ecritures);
        return $this->render('Default/banques.html.twig', array(
            'compte' => $compte,
            'ecritures' => $ecritures,
            'chartjss' => $chartjss
        ));
    }


    /**
     * @Route("/compte/{compte_id}", name="compte")
     **/
    public function compteEditAction($compte_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $operations = [];
        if($compte_id == '0'){
            $compte = new Compte();
            $compte->company = $this->company;
        } else {
            $compte = $em->getRepository('App:Compte')->findOneById($compte_id);
            $operations = $em->getRepository('App:Operation')->getAllForCompte($compte);
            $ecritures = [];
            $ecritures_futures = [];
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

                        if($operation->date > new DateTime()){
                            $ecritures_futures[] = $ecriture;
                        } else {
                            $ecritures[] = $ecriture;
                        }

                    }


                }

            }
            $ecritures = array_reverse($ecritures);
            $ecritures_futures = array_reverse($ecritures_futures);
        }
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($compte);
            $em->flush();
            return $this->redirectToRoute('comptes');
        }
        $session->set("redirect_url_facture", $this->generateUrl('compte', array('compte_id' => $compte_id)));
        return $this->render('Default/compte.html.twig', array(
            'form' => $form->createView(),
            'compte' => $compte,
            'ecritures' => $ecritures,
            'ecritures_futures' => $ecritures_futures
        ));
    }

    /**
     * @Route("/operations", name="operations")
     */
    public function operationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $operations = $em->getRepository('App:Operation')->getAll();

        return $this->render('Default/operations.html.twig', array(
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
            $operation = $em->getRepository('App:Operation')->findOneById($operation_id);
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
        return $this->render('Default/operation.html.twig', array(
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
        $em->getRepository('App:Operation')->delete($operation_id);
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
            $ecriture->operation = $em->getRepository('App:Operation')->findOneById($operation_id);;
        } else {
            $ecriture = $em->getRepository('App:Ecriture')->findOneById($ecriture_id);
        }
        $campagnes = $em->getRepository('App:Campagne')->getAllAndNullforCompany($this->company);
        $form = $this->createForm(EcritureType::class, $ecriture, array(
            'comptes' => $em->getRepository('App:Compte')->getAll(),
            'campagnes' => $campagnes
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($ecriture);
            $em->flush();
            return $this->redirectToRoute('operation', array('operation_id' => $operation_id));
        }
        return $this->render('base_form.html.twig', array(
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
        $facture_fournisseurs = $em->getRepository('App:FactureFournisseur')->getAll();

        return $this->render('Default/facture_fournisseurs.html.twig', array(
            'facture_fournisseurs' => $facture_fournisseurs
        ));
    }

    /**
     * @Route("/facture_fournisseur/{facture_id}/delete_pdf", name="facture_fournisseur_delete_pdf")
     **/
    public function factureFournisseurDeletePdfAction($facture_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('App:FactureFournisseur')->findOneById($facture_id);
        $facture->factureFile = null;
        $em->getRepository('App:FactureFournisseur')->save($facture);
        return $this->redirectToRoute('facture_fournisseur', array('facture_id' => $facture_id));
    }

    /**
     * @Route("/facture_fournisseur/{facture_id}", name="facture_fournisseur")
     **/
    public function factureFournisseurAction($facture_id, Request $request)
    {
        $this->check_user($request);
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $operations = [];
        if($facture_id == '0'){
            $facture = new FactureFournisseur();
            $facture->date = new Datetime();
        } else {
            $facture = $em->getRepository('App:FactureFournisseur')->findOneById($facture_id);
            $operations = $em->getRepository('App:Operation')->getForFacture($facture);
        }
        if($facture->type == "V"){
            $facture->type = "Vente";
            $facture->montantTTC = -$facture->montantTTC;
            $facture->montantHT = -$facture->montantHT;
        } else {
            $facture->type = "Achat";
        }
        $campagnes = $em->getRepository('App:Campagne')->getAllAndNullforCompany($this->company);
        $banques = $em->getRepository('App:Compte')->getAllBanques($this->company);
        $comptes = $em->getRepository('App:Compte')->getNoBanques($this->company);
        $form = $this->createForm(FactureFournisseurType::class, $facture, array(
            'banques' => $banques,
            'comptes' => $comptes,
            'campagnes' => $campagnes
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            if($facture->type == "Vente"){
                $facture->type = "V";
                $facture->montantTTC = -$facture->montantTTC;
                $facture->montantHT = -$facture->montantHT;
            } else {
                $facture->type == "A";
            }
            $em->getRepository('App:FactureFournisseur')->save($facture);

            $redirect_url_facture = $session->get("redirect_url_facture");
            if($redirect_url_facture){
                return $this->redirect($redirect_url_facture);
            }
            return $this->redirectToRoute('factures_fournisseurs');
        }
        return $this->render('Default/facture_fournisseur.html.twig', array(
            'form' => $form->createView(),
            'facture' => $facture,
            'operations' => $operations
        ));
    }

    /**
     * @Route("export", name="export")
     **/
    public function factureFournisseurExportAction(Request $request)
    {
        $files = array();
        $em = $this->getDoctrine()->getManager();

        $zip = new \ZipArchive();
        $zipName = 'Documents_'.time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);

        foreach ($em->getRepository('App:FactureFournisseur')->findAll() as $f) {
            if($f->factureFile){
                $file = $f->factureFile;
                $str = strtolower($f->name);
                $str = str_replace(" - ", '_', $str);
                $str = str_replace(' ', '_', $str);
                $str = str_replace('-', '', $str);
                $str = str_replace('/', '_', $str);
                $str = str_replace('&', '_', $str);

                $fileName = $f->date->format('Ymd').'_'.$str;
                $zip->addFile($this->getParameter('factures_directory').'/'.$file, $fileName.'.pdf');
            }
        }
        foreach ($files as $f) {
            $zip->addFromString(basename($f),  file_get_contents($f));
        }
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }

    /**
     * @Route("/facture_fournisseur/{facture_id}/delete", name="facture_fournisseur_delete")
     **/
    public function factureFournisseurDeleteAction($facture_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:FactureFournisseur')->delete($facture_id);
        return $this->redirectToRoute('factures_fournisseurs');
    }
}
