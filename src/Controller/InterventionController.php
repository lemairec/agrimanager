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
        $comment = "";

        $intervention = $em->getRepository('App:Intervention')->find($intervention_id);
        if($intervention){
            $date = $intervention->date;
            $type = $intervention->type;
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

        dump($parcelles);

        $produits = $em->getRepository('App:Produit')->getAllName($campagne);

        return $this->render('Default/intervention2.html.twig', array(
            'id' => $intervention_id,
            'date' => $date->format('d/m/Y'),
            'type' => $type,
            'comment' => $comment,
            'produits' => $produits,
            'produitsIntervention' => $produitsIntervention,
            'parcelles' => $parcelles
        ));
    }

    /**
     * @Route("/api/intervention")
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
        $intervention->date = DateTime::createFromFormat('d/m/Y', $data["date"]);
        $em->persist($intervention);
        $em->flush();

        foreach($data["parcelles"] as $parcelle){
            if($parcelle["checked"]){
                $it = new InterventionParcelle();
                $it->intervention = $intervention;
                $it->parcelle = $em->getRepository('App:Parcelle')->find($parcelle["id"]);
                $em->getRepository('App:InterventionParcelle')->save($it);
            }
        }

        foreach($data["produits"] as $produit){
            $it = new InterventionProduit();
            $it->intervention = $intervention;
            $it->name = ($produit["name"]);
            $it->qty = ($produit["qty"]);
            $em->getRepository('App:InterventionProduit')->save($it, $campagne);
        }

        return new JsonResponse("OK");

    }

}
