<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Parcelle;
use App\Entity\Ilot;
use App\Entity\Intervention;
use App\Entity\Log;
use App\Entity\MetaCulture;

use App\Form\UserType;
use App\Form\CompanyAdminType;
use App\Form\MetaCultureType;

class PPFController extends CommonController
{
    #[Route(path: 'parcelles_ppf', name: 'parcelles_ppf')]
    public function parcellesPPFAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];
        $total = 0;

        $is = $em->getRepository(Ilot::class)->getAllForCompany($campagne->company);
        $ilots = [];
        foreach ($is as $i) {
            $ilots[] = ["id" => $i->id, "name" => $i->name, "surface_totale" => $i->surface, "surface" => 0];
        }

        $parcelles = $em->getRepository(Parcelle::class)->getAllForCampagne($campagne);
        foreach ($parcelles as $p) {
            if($p->active && $p->culture){
                if (!array_key_exists($p->getCultureName(), $cultures)) {
                    $cultures[$p->getCultureName()] = 0;
                }
                $cultures[$p->getCultureName()] += $p->surface;
                $total += $p->surface;
            }
            if($p->ilot){
                for ( $i = 0; $i< count($ilots);++$i) {
                    if($ilots[$i]["id"] == $p->ilot->id){
                        $ilots[$i]["surface"] = $ilots[$i]["surface"] + $p->surface;
                    }
                }
            }

            $p->ppfObjRendement = $p->culture->ppfObjRendement;
            $p->ppfAzoteUnite = $p->culture->ppfAzoteUnite;
            $p->ppfBesoinCulture = $p->ppfObjRendement*$p->ppfAzoteUnite;
            $p->ppfMiseEnReserve = $p->culture->ppfMiseEnReserve;
            $p->ppfBesoinTotal = $p->ppfBesoinCulture + $p->ppfMiseEnReserve;
            
        }

        $ilots2 = [];
        foreach ($ilots as $ilot){
            if($ilot["surface_totale"] > $ilot["surface"]){
                $ilot["surface_restante"] = $ilot["surface_totale"]-$ilot["surface"];
                $ilots2[] = $ilot;
            }
        }
        if($total == 0){
            $total = 1;//todo berk
        }

        return $this->render('Default/parcelles_ppf.html.twig', array(
            'ilots' => $ilots2,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
            'total' => $total,
            'navs' => ["Parcelles" => "parcelles"]
        ));
    }
    
}
