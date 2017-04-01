<?php

namespace AgriBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


use AgriBundle\Entity\Ilot;
use AgriBundle\Entity\Intervention;
use AgriBundle\Entity\InterventionParcelle;
use AgriBundle\Entity\InterventionProduit;
use AgriBundle\Repository\IlotRepository;
use AgriBundle\Entity\Company;
use AgriBundle\Entity\Produit;
use Datetime;

use AgriBundle\Form\InterventionType;
use AgriBundle\Form\InterventionParcelleType;
use AgriBundle\Form\InterventionProduitType;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $advertRepository = $em->getRepository('AgriBundle:Company');
        return $this->render('AgriBundle:Default:index.html.twig');
    }

    /**
     * @Route("/ilots")
     */
    public function ilotsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ilots = $em->getRepository('AgriBundle:Ilot')->findBy(array(), array('surface' => 'desc'));
        $sum_ilots = array_reduce($ilots, function($i, $obj)
        {
                return $i += $obj->surface;
        });

        return $this->render('AgriBundle:Default:ilots.html.twig', array(
                    'ilots' => $ilots,
                    'sum_ilots' => $sum_ilots,
                        ));
    }

    /**
     * @Route("/parcelles/{campagne_id}")
     */
    public function parcelles($campagne_id)
    {
        $em = $this->getDoctrine()->getManager();

        $cultures = [];

        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagne(2017);
        foreach ($parcelles as $p) {
            if (!array_key_exists($p->culture, $cultures)) {
                $cultures[$p->culture] = 0;
            }
            $cultures[$p->culture] += $p->surface;
        }
        return $this->render('AgriBundle:Default:parcelles.html.twig', array(
                    'parcelles' => $parcelles,
                    'cultures' => $cultures,
                        ));
    }

    /**
     * @Route("/interventions/{campagne_id}", name="interventions")
     */
    public function interventions($campagne_id)
    {
        $em = $this->getDoctrine()->getManager();
        $interventions = $em->getRepository('AgriBundle:Intervention')->getAll();
        return $this->render('AgriBundle:Default:interventions.html.twig', array(
                    'interventions' => $interventions,
                        ));
    }

    /**
     * @Route("/intervention/{intervention_id}", name="intervention")
     **/
    public function interventionEditAction($intervention_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($intervention_id == 0){
            $intervention = new Intervention();
            $intervention->date = new \Datetime();
            $intervention->type = "phyto";
            $em->persist($intervention);
            $em->flush();
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention->id));
        } else {
            $intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        }
        $form = $this->createForm(InterventionType::class, $intervention);
        $form->handleRequest($request);


        if ($form->isValid()) {
            foreach($intervention->parcelles as $p){
                $p->intervention = $intervention;
            }
            $em->persist($intervention);
            $em->flush();
            return $this->redirectToRoute('interventions', array('campagne_id' => 2012));
        }
        return $this->render('AgriBundle:Default:intervention.html.twig', array(
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
        $intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        $em->remove($intervention);
        $em->flush();
        return $this->redirectToRoute('interventions', array('campagne_id' => 2012));
    }

    /**
     * @Route("/intervention/{intervention_id}/parcelle/{intervention_parcelle_id}", name="intervention_parcelle")
     **/
    public function interventionParcelleAction($intervention_id, $intervention_parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($intervention_parcelle_id== 0){
            $intervention_parcelle = new InterventionParcelle();
            $intervention_parcelle->intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        } else {
            //$intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        }
        $form = $this->createForm(InterventionParcelleType::class, $intervention_parcelle);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em->persist($intervention_parcelle);
            $em->flush();
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/intervention/{intervention_id}/produit/{intervention_produit_id}", name="intervention_produit")
     **/
    public function interventionProduitAction($intervention_id, $intervention_produit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($intervention_produit_id== 0){
            $intervention_produit = new InterventionProduit();
            $intervention_produit->intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        } else {
            //$intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        }
        $form = $this->createForm(InterventionProduitType::class, $intervention_produit);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em->persist($intervention_produit);
            $em->flush();
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
