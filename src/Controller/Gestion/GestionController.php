<?php

namespace App\Controller\Gestion;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use App\Entity\Gestion\Compte;
use App\Entity\Gestion\Ecriture;
use App\Entity\Gestion\Operation;
use App\Entity\Gestion\FactureFournisseur;
use App\Form\Gestion\CompteType;
use App\Form\Gestion\EcritureType;
use App\Form\Gestion\OperationType;
use App\Form\Gestion\FactureFournisseurType;
use Symfony\Component\HttpFoundation\File\File;

//COMPTE
//ECRITURE
//OPERATION


class GestionController extends CommonController
{
    public function getColor(){
        return ["#99ccff", "#ff9966", "#B6E17B", ""];
    }

    public function getDateColor(){
        return ["" => "", "2016" => "#99ccff", "2017" => "#ff9966", "2018" => "#B6E17B", "2019"=> "#00909e", "2020"=> "#408080", "2021"=> ""];
    }

    public function getDataCampagne($campagne, $ecritures){
        $data = [];

        $value = 0;
        foreach($ecritures as $ecriture){
            $year = intVal($ecriture['date']->format("Y"))-intVal($campagne)+2017;
            $value += $ecriture['value'];
            $data[] = ['date' => $ecriture['date']->format("d-m")."-".$year, 'value' => $value];
        }
        return ['annee'=> $campagne, 'data' => $data, 'color' => $this->getDateColor()[$campagne]];
    }

    public function getDataWithDates($ecritures){
        $chartjss = [];
        $chartjs = NULL;
        $year = 0;
        
        foreach($ecritures as $ecriture){
            $new_year = $ecriture['date']->format('Y');
            if($year != $new_year){
                $year = $new_year;
                if($chartjs){
                    $chartjss[] = $chartjs;
                }
                $chartjs = ['annee'=> $year, 'data' => [], 'color' => $this->getDateColor()[$year]];
            }
            
            $chartjs['data'][] = ['date' => $ecriture['date']->format("d-m")."-2017", 'value' => $ecriture['sum_value'] ];
        }
        $chartjss[] = $chartjs;
        return $chartjss;
    }

    public function getDataSerieChartJs($ecritures, $name, $i){
        $chartjs = ['annee'=> $name, 'data' => [], 'color' => $this->getColor()[$i]];
        $value=0;
        foreach($ecritures as $ecriture){
            $value += $ecriture['value'];
            $chartjs['data'][] = ['date' => $ecriture['date']->format("d-m-Y"), 'value' => $ecriture['sum_value'] ];
        }
        return $chartjs;
    }

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

        return $this->render('Gestion/cours.html.twig', array(
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
        return $this->render('Gestion/cours_new.html.twig', array(
            'date' => $date->format("d/m/Y"),
            'courss' => $courss,
        ));
    }

    /**
     * @Route("/comptes2", name="comptes2")
     */
    public function comptes2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $c = $this->getCurrentCampagne($request);

        $comptes = $em->getRepository('App:Gestion\Compte')->getAllForCompany($this->company);

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

        return $this->render('Gestion/comptes.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'comptes' => $comptes,
            'comptes_campagnes' => $comptes_campagnes
        ));
    }

    /**
     * @Route("/comptes", name="comptes")
     */
    public function comptesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $c = $this->getCurrentCampagne($request);

        $comptes = $em->getRepository('App:Gestion\Compte')->getAllForCompany($this->company);

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

        return $this->render('Gestion/comptes.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'comptes' => $comptes,
            'comptes_campagnes' => $comptes_campagnes
        ));
    }

    /**
     * @Route("/comptes_by_year", name="comptes_by_year")
     */
    public function comptesByYearAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $operations = $em->getRepository('App:Gestion\Operation')->findAll();
        $comptes = $em->getRepository('App:Gestion\Compte')->getAll();
        $years = ['2020', '2019', '2018','2017', '2016'];


        //dump($comptes);

        $res2 = [];
        foreach($comptes as $c){
            $compte = strval($c);
            $res2[$compte] = [];
            foreach($years as $y){
                $res2[$compte][$y] = 0;
            }
        }


        foreach ($operations as $o) {
            $year = $o->date->format('Y');
            foreach($o->ecritures as $e){
                $compte = strval($e->compte);
                $res2[$compte][$year] += $e->value;
            }
        }

        return $this->render('Gestion/comptes_by_year.html.twig', array(
            'res' => $res2,
            'years' => $years,
            'comptes' => $comptes
        ));
    }

    /**
     * @Route("/banque", name="banque")
     **/
    public function banqueEditAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $operations = [];
        $ecritures = [];
        $ecritures_futures = [];
        {
            $operations = $em->getRepository('App:Gestion\Operation')->getAllForBanque($this->company);
            $value = 0;
            $l = count($operations);
            for($i = 0; $i < $l; ++$i){
                $operation = $operations[$i];
                foreach($operation->ecritures as $e){
                    if($e->compte->type == "banque"){
                        $ecriture = ['operation_id'=>$operation->id,'date'=>$operation->date, 'name'=>$operation->name, 'value'=>-$e->value];
                        $ecriture['campagne'] = "";
                        $ecriture['facture'] = $operation->facture;
                        if($e->campagne){
                            $ecriture['campagne'] = $e->campagne->name;
                        }

                        $value += $ecriture['value'];
                        $ecriture['sum_value'] = $value;

                        $ecritures[] = $ecriture;
                    }

                }

            }
        }
        
        $chartjss = $this->getDataWithDates($ecritures);
        $ecritures = array_reverse($ecritures);
        
        return $this->render('Gestion/banques.html.twig', array(
            'ecritures' => $ecritures,
            'chartjss' => $chartjss
        ));
    }

    /**
     * @Route("/emprunt", name="emprunt")
     **/
    public function empruntEditAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $operations = $em->getRepository('App:Gestion\Operation')->getAllAsc();
        $chartjss = [];
        $ecritures = [];
        
        $value = 0;
        $l = count($operations);
        for($i = 0; $i < $l; ++$i){
            $operation = $operations[$i];
            foreach($operation->ecritures as $e){
                if($e->compte->type == "banque"){
                    $ecriture = ['operation_id'=>$operation->id,'date'=>$operation->date, 'name'=>$operation->name, 'value'=>-$e->value];
                    $ecriture['campagne'] = "";
                    $ecriture['facture'] = $operation->facture;
                    if($e->campagne){
                        $ecriture['campagne'] = $e->campagne->name;
                    }

                    $value += $ecriture['value'];
                    $ecriture['sum_value'] = $value;
                    $ecritures[] = $ecriture;
                }

            }
        }
        $chartjss[] = $this->getDataSerieChartJs($ecritures, "banque", 0);

        $ecritures = [];
        $value = 0;
        $l = count($operations);
        for($i = 0; $i < $l; ++$i){
            $operation = $operations[$i];
            foreach($operation->ecritures as $e){
                if($e->compte->type == "emprunt"){
                    $ecriture = ['operation_id'=>$operation->id,'date'=>$operation->date, 'name'=>$operation->name, 'value'=>-$e->value];
                    $ecriture['campagne'] = "";
                    $ecriture['facture'] = $operation->facture;
                    if($e->campagne){
                        $ecriture['campagne'] = $e->campagne->name;
                    }

                    $value += $ecriture['value'];
                    $ecriture['sum_value'] = $value;

                    $ecriture['sum_value'] = $value;
                    $ecritures[] = $ecriture;
                }

            }

        }
        $chartjss[] = $this->getDataSerieChartJs($ecritures, "emprunt", 1);

        
        $ecritures = array_reverse($ecritures);
        
        return $this->render('Gestion/banques.html.twig', array(
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
        $ecritures_by_campagne = [];
        $ecritures = [];
        $ecritures_futures = [];
        if($compte_id == '0'){
            $compte = new Compte();
            $compte->company = $this->company;
        } else {
            $compte = $em->getRepository('App:Gestion\Compte')->findOneById($compte_id);
            $operations = $em->getRepository('App:Gestion\Operation')->getAllForCompte($compte);
            $value = 0;
            $l = count($operations);
            for($i = 0; $i < $l; ++$i){
                $operation = $operations[$i];
                foreach($operation->ecritures as $e){
                    if($e->compte == $compte){
                        $campagne = "";
                        if($e->campagne){
                            $campagne = $e->campagne->name;
                        }
                        $ecriture = ['operation_id'=>$operation->id,'date'=>$operation->date, 'name'=>$operation->name, 'value'=>$e->value, 'campagne'=>$campagne];
                        $ecriture['facture'] = $operation->facture;
                        
                        if($compte->type == 'banque'){
                            $ecriture['value'] = -$ecriture['value'];
                        }

                        $value += $ecriture['value'];
                        $ecriture['sum_value'] = $value;

                        $ecritures[] = $ecriture;
                        if(!array_key_exists($campagne, $ecritures_by_campagne)){
                            $ecritures_by_campagne[$campagne] = ["value" => 0, "ecritures" => []]; 
                        }
                        $ecritures_by_campagne[$campagne]["ecritures"][] = $ecriture;
                        $ecritures_by_campagne[$campagne]["value"] += $ecriture['value'];
                    }

                }

            }
        }
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        $chartjss = [];
        $chartjs = NULL;
        $year = 0;
        $value = 0;
        if(array_key_exists("", $ecritures_by_campagne)){
            $chartjss = $this->getDataWithDates($ecritures_by_campagne[""]["ecritures"]);
        } else {
            foreach($ecritures_by_campagne as $k => $value){
                $chartjss[] = $this->getDataCampagne($k, $value["ecritures"]);
            }
        }

        $ecritures = array_reverse($ecritures);
        $ecritures_futures = array_reverse($ecritures_futures);
    
        if ($form->isSubmitted()) {
            $em->persist($compte);
            $em->flush();
            return $this->redirectToRoute('comptes');
        }
        $session->set("redirect_url_facture", $this->generateUrl('compte', array('compte_id' => $compte_id)));
        return $this->render('Gestion/compte.html.twig', array(
            'form' => $form->createView(),
            'compte' => $compte,
            'ecritures' => $ecritures,
            'ecritures_futures' => $ecritures_futures,
            'chartjss' => $chartjss
        ));
    }

    /**
     * @Route("/operations", name="operations")
     */
    public function operationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $operations = $em->getRepository('App:Gestion\Operation')->getAllForCompany($this->company);

        return $this->render('Gestion/operations.html.twig', array(
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
            $operation->company = $this->company;
            $operation->ecritures = [];
            $em->persist($operation);
            $em->flush();
        } else {
            $operation = $em->getRepository('App:Gestion\Operation')->findOneById($operation_id);
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
        return $this->render('Gestion/operation.html.twig', array(
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
        $em->getRepository('App:Gestion\Operation')->delete($operation_id);
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
            $ecriture->operation = $em->getRepository('App:Gestion\Operation')->findOneById($operation_id);;
        } else {
            $ecriture = $em->getRepository('App:Gestion\Ecriture')->findOneById($ecriture_id);
        }
        $campagnes = $em->getRepository('App:Campagne')->getAllAndNullforCompany($this->company);
        $form = $this->createForm(EcritureType::class, $ecriture, array(
            'comptes' => $em->getRepository('App:Gestion\Compte')->getAllForCompany($this->company),
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
        $facture_fournisseurs = $em->getRepository('App:Gestion\FactureFournisseur')->getAllForCompany($this->company);

        return $this->render('Gestion/facture_fournisseurs.html.twig', array(
            'facture_fournisseurs' => $facture_fournisseurs
        ));
    }

    /**
     * @Route("/facture_fournisseur/{facture_id}/delete_pdf", name="facture_fournisseur_delete_pdf")
     **/
    public function factureFournisseurDeletePdfAction($facture_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('App:Gestion\FactureFournisseur')->findOneById($facture_id);
        $facture->factureFile = null;
        $em->getRepository('App:Gestion\FactureFournisseur')->save($facture);
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
            $facture->company = $this->company;
            $facture->date = new Datetime();
        } else {
            $facture = $em->getRepository('App:Gestion\FactureFournisseur')->findOneById($facture_id);
            $operations = $em->getRepository('App:Gestion\Operation')->getForFacture($facture);
        }
        if($facture->type == "V"){
            $facture->type = "Vente";
            $facture->montantTTC = -$facture->montantTTC;
            $facture->montantHT = -$facture->montantHT;
        } else {
            $facture->type = "Achat";
        }

        $campagnes = $em->getRepository('App:Campagne')->getAllAndNullforCompany($this->company);
        $banques = $em->getRepository('App:Gestion\Compte')->getAllBanques($this->company);
        $comptes = $em->getRepository('App:Gestion\Compte')->getNoBanques($this->company);
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
            $em->getRepository('App:Gestion\FactureFournisseur')->save($facture);

            $redirect_url_facture = $session->get("redirect_url_facture");
            if($redirect_url_facture){
                return $this->redirect($redirect_url_facture);
            }
            return $this->redirectToRoute('factures_fournisseurs');
        }
        return $this->render('Gestion/facture_fournisseur.html.twig', array(
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
        $facture = $em->getRepository('App:Gestion\FactureFournisseur')->findOneById($facture_id);
        $em->getRepository('App:Gestion\FactureFournisseur')->delete($facture);
        return $this->redirectToRoute('factures_fournisseurs');
    }
}
