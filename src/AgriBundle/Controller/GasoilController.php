<?php

namespace AgriBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;
use AgriBundle\Controller\CommonController;

use AgriBundle\Entity\Gasoil;
use AgriBundle\Form\GasoilType;


class GasoilController extends CommonController
{
    /**
     * @Route("gasoils", name="gasoils")
     */
    public function gasoilsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $gasoils = $em->getRepository('AgriBundle:Gasoil')->getAllForCampagne($campagne);
        $gasoilsType = $em->getRepository('AgriBundle:Gasoil')->getAllforCompanyGroupByType($this->company);

        $gasoilsAll = $em->getRepository('AgriBundle:Gasoil')->getAllForCompany($this->company);


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

        return $this->render('AgriBundle:Default:gasoils.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'gasoils' => $gasoils,
            'gasoilsType' => $gasoilsType,
            'campagnes_g' => $campagnes_g
        ));
    }

    /**
     * @Route("/gasoil/{gasoil_id}", name="gasoil")
     **/
    public function gasoilEditAction($gasoil_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $materiels = $em->getRepository('AgriBundle:Materiel')->getAllForCompany($this->company);
        $materiels[] = null;
        if($gasoil_id == '0'){
            $gasoil = new Gasoil();
            $gasoil->campagne = $campagne;
            $gasoil->date = new \DateTime();
        } else {
            $gasoil = $em->getRepository('AgriBundle:Gasoil')->findOneById($gasoil_id);
        }
        $form = $this->createForm(GasoilType::class, $gasoil, ['materiels'=>$materiels]);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $gasoil = $em->getRepository('AgriBundle:Gasoil')->save($gasoil);
            return $this->redirectToRoute('gasoils');
        }
        return $this->render('AgriBundle:Default:gasoil.html.twig', array(
            'form' => $form->createView()
        ));
    }
}