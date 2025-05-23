<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;
use App\Controller\CommonController;

use App\Entity\Gasoil;
use App\Form\GasoilType;


class GasoilController extends CommonController
{
    #[Route(path: 'gasoils', name: 'gasoils')]
    public function gasoilsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $gasoils = $em->getRepository(Gasoil::class)->getAllForCampagne($campagne);
        $gasoilsType = $em->getRepository(Gasoil::class)->getAllforCompanyGroupByType($this->company);

        $gasoilsAll = $em->getRepository(Gasoil::class)->getAllForCompany($this->company);
        $gasoils2 = [];

        $campagnes_g = [];

        foreach ($gasoilsAll as $g) {
            $c = $g->campagne;
            if (!array_key_exists($c->name, $campagnes_g)) {
                $campagnes_g[$c->name] = 0;
            }
            if($g->litre < 0 && $g->comment != "ignore"){
                $campagnes_g[$c->name] += $g->litre;
            }
        }

        $total = 0;
        foreach ($gasoils as $g) {
            if($g->litre < 0){
                $total = $total - $g->litre;

            }
            $g->litrePompeCalc = $total;
            $gasoils2[] = $g;
            if($g->litrePompe){
                $total = $g->litrePompe;
            }


        }


        return $this->render('Default/gasoils.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'gasoils' => array_reverse($gasoils2),
            'gasoilsType' => $gasoilsType,
            'campagnes_g' => $campagnes_g
        ));
    }

    #[Route(path: 'gasoils_all', name: 'gasoils_all')]
    public function gasoilsAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $gasoilsAll = $em->getRepository(Gasoil::class)->getAllForCompany($this->company);

        return $this->render('Default/gasoils_all.html.twig', array(
            'gasoilsAll' => $gasoilsAll
        ));
    }

    #[Route(path: '/gasoil/{gasoil_id}', name: 'gasoil')]
    public function gasoilEditAction($gasoil_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $materiels = $em->getRepository(Materiel::class)->getAllForCompany($this->company);
        $materiels[] = null;
        if($gasoil_id == '0'){
            $gasoil = new Gasoil();
            $gasoil->campagne = $campagne;
            $gasoil->date = new \DateTime();
        } else {
            $gasoil = $em->getRepository(Gasoil::class)->findOneById($gasoil_id);
        }
        $form = $this->createForm(GasoilType::class, $gasoil, ['materiels'=>$materiels]);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $gasoil = $em->getRepository(Gasoil::class)->save($gasoil);
            return $this->redirectToRoute('gasoils');
        }
        return $this->render('Default/gasoil.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
