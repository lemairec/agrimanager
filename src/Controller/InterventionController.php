<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Datetime;

use App\Controller\CommonController;

use App\Entity\Intervention;
use App\Entity\InterventionParcelle;
use App\Entity\InterventionProduit;

class InterventionController extends CommonController
{
    /**
     * @Route("/interventions", name="interventions")
     */
    public function interventions(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $interventions = $em->getRepository('App:Intervention')->getAllForCampagne($campagne);
        return $this->render('Default/interventions.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'interventions' => $interventions,
            'navs' => ["Interventions" => "interventions"]
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}", name="intervention")
     **/
    public function intervention2EditAction($intervention_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $parcelles2 =  $em->getRepository('App:Parcelle')->getAllForCampagne($campagne);

        $parcelles = [];
        $produitsIntervention = [];
        $date = new \Datetime();
        $type = "";
        $name = "";
        $comment = "";

        $intervention = $em->getRepository('App:Intervention')->find($intervention_id);
        if($intervention){
            if($campagne->id != $intervention->campagne->id){
                $campagne = $intervention->campagne;
                $parcelles2 =  $em->getRepository('App:Parcelle')->getAllForCampagne($campagne);
            }
            $date = $intervention->date;
            $type = $intervention->type;
            $name = $intervention->name;
            $comment = $intervention->comment;
            foreach($intervention->produits as $produit){
                $produitsIntervention[] = ["name" => $produit->name, "qty" => $produit->qty];
            }
            foreach($parcelles2 as $parcelle){
                $checked = false;
                foreach($intervention->parcelles as $p){
                    if($parcelle->id == $p->parcelle->id){
                        $checked = true;
                    }
                }

                $parcelles[] = ["id" => $parcelle->id, "name" => $parcelle->completeName, "surface"=>$parcelle->surface, "checked"=>$checked];
            }
        } else {
            foreach($parcelles2 as $parcelle){
                $parcelles[] = ["id" => $parcelle->id, "name" => $parcelle->completeName, "surface"=>$parcelle->surface, "checked"=>false];
            }
        }

        $produits = $em->getRepository('App:Produit')->getAllName($campagne);

        return $this->render('Default/intervention2.html.twig', array(
            'id' => $intervention_id,
            'date' => $date->format('d/m/Y'),
            'type' => $type,
            'name' => $name,
            'comment' => $comment,
            'produits' => $produits,
            'produitsIntervention' => $produitsIntervention,
            'parcelles' => $parcelles,
            'navs' => ["Interventions" => "interventions"]
        ));
    }

    /**
     * @Route("/api/intervention", name="intervention_api")
     */
    public function annoncesApiAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $data = $data["intervention"];

        $intervention = $em->getRepository('App:Intervention')->find($data["id"]);
        if($intervention){
            $intervention_parcelles = $em->getRepository('App:InterventionParcelle')
                                       ->findBy(array('intervention'=>$intervention));
            $intervention_produits = $em->getRepository('App:InterventionProduit')
                                       ->findBy(array('intervention'=>$intervention));
            foreach ($intervention_produits as $it) {
                $em->getRepository('App:InterventionProduit')->delete($it->id);
            }
            foreach ($intervention_parcelles as $it) {
                $em->remove($it);
            }

        } else {
            $intervention = new Intervention();
            $intervention->campagne = $campagne;
            $intervention->company = $campagne->company;
        }
        $intervention->surface = 0;
        $intervention->type = $data["type"];
        $intervention->comment = $data["comment"];
        $intervention->name = $data["name"];
        $intervention->date = DateTime::createFromFormat('d/m/Y', $data["date"]);
        $em->persist($intervention);
        $em->flush();

        foreach($data["parcelles"] as $parcelle){
            if($parcelle["checked"]){
                $it = new InterventionParcelle();
                $it->intervention = $intervention;
                $it->parcelle = $em->getRepository('App:Parcelle')->find($parcelle["id"]);
                $em->persist($it);
                $em->flush();
                $intervention->surface += $it->parcelle->surface;
            }
        }

        foreach($data["produits"] as $produit){
            $it = new InterventionProduit();
            $it->intervention = $intervention;
            $it->name = ($produit["name"]);
            $it->qty = ($produit["qty"]);
            $em->getRepository('App:InterventionProduit')->save($it, $campagne);
        }



        $em->persist($intervention);
        $em->flush();


        if($data["id"] == '0'){
            $this->mylog2("Création de l'intervention du ".$data["date"]." ".$intervention->name, $data);
        } else {
            $this->mylog2("Modification de l'intervention du ".$data["date"]." ".$intervention->name, $data);
        }

        return new JsonResponse("OK");

    }


    /**
     * @Route("/intervention2/{intervention_id}", name="intervention2")
     **/
    public function interventionEditAction($intervention_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($intervention_id == '0'){
            $intervention = new Intervention();
            $intervention->date = new \Datetime();
            $intervention->type = "phyto";
            $intervention->comment = "";
            $intervention->surface = 0;
            $intervention->company = $this->company;
            $intervention->campagne = $campagne;
            $em->persist($intervention);
            $em->flush();
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention->id));
        } else {
            $intervention = $em->getRepository('App:Intervention')->findOneById($intervention_id);
        }
        $form = $this->createForm(InterventionType::class, $intervention);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            foreach($intervention->parcelles as $p){
                $p->intervention = $intervention;
            }
            $em->persist($intervention);
            $em->flush();
            return $this->redirectToRoute('interventions');
            //$response = new Response();
            //$response->setStatusCode(Response::HTTP_OK);
            //return $response;
        }
        return $this->render('Default/intervention.html.twig', array(
            'form' => $form->createView(),
            'intervention' => $intervention,
            'parcelles' => $intervention->parcelles
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}/delete", name="intervention_delete")
     **/
    public function interventionDeleteAction($intervention_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:Intervention')->delete($intervention_id);
        return $this->redirectToRoute('interventions');
    }

    /**
     * @Route("/intervention/{intervention_id}/parcelle/{intervention_parcelle_id}", name="intervention_parcelle")
     **/
    public function interventionParcelleAction($intervention_id, $intervention_parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($intervention_parcelle_id == '0'){
            $intervention_parcelle = new InterventionParcelle();
            $intervention_parcelle->intervention = $em->getRepository('App:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_parcelle = $em->getRepository('App:InterventionParcelle')->findOneById($intervention_parcelle_id);
        }
        $parcelles =  $em->getRepository('App:Parcelle')->getAllForCampagne($campagne);
        $form = $this->createForm(InterventionParcelleType::class, $intervention_parcelle, array(
            'parcelles' => $parcelles
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->getRepository('App:InterventionParcelle')->save($intervention_parcelle);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}/parcelle/{intervention_parcelle_id}/delete", name="intervention_parcelle_delete")
     **/
    public function interventionParcelleDeleteAction($intervention_id, $intervention_parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:InterventionParcelle')->delete($intervention_parcelle_id);
        return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
    }

    /**
     * @Route("/intervention/{intervention_id}/produit/{intervention_produit_id}", name="intervention_produit")
     **/
    public function interventionProduitAction($intervention_id, $intervention_produit_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        if($intervention_produit_id == '0'){
            $intervention_produit = new InterventionProduit();
            $intervention_produit->intervention = $em->getRepository('App:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_produit = $em->getRepository('App:InterventionProduit')->findOneById($intervention_produit_id);
        }
        $form = $this->createForm(InterventionProduitType::class, $intervention_produit);
        $form->handleRequest($request);
        $produits = $em->getRepository('App:Produit')->getAllName($campagne);

        if ($form->isSubmitted()) {
            $em->getRepository('App:InterventionProduit')->save($intervention_produit, $campagne);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('Default/intervention_produit.html.twig', array(
            'form' => $form->createView(),
            'produits' => $produits,
            'surface_totale' => $intervention_produit->intervention->surface

        ));
    }

    /**
     * @Route("/intervention/{intervention_id}/materiel/{intervention_materiel_id}", name="intervention_materiel")
     **/
    public function interventionMaterielAction($intervention_id, $intervention_materiel_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        if($intervention_materiel_id == '0'){
            $intervention_materiel = new InterventionMateriel();
            $intervention_materiel->intervention = $em->getRepository('App:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_materiel = $em->getRepository('App:InterventionMateriel')->findOneById($intervention_materiel);
        }
        $materiels =  $em->getRepository('App:Materiel')->getAllForCompany($this->company);
        $form = $this->createForm(InterventionMaterielType::class, $intervention_materiel, array(
            'materiels' => $materiels
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->getRepository('App:InterventionMateriel')->save($intervention_materiel);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}/produit/{intervention_produit_id}/delete", name="intervention_produit_delete")
     **/
    public function interventionProduitDeleteAction($intervention_id, $intervention_produit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:InterventionProduit')->delete($intervention_produit_id);
        return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
    }

}
