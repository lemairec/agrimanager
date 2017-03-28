<?php

namespace AgriBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


use AgriBundle\Entity\Ilot;
use AgriBundle\Entity\Intervention;
use AgriBundle\Repository\IlotRepository;
use AgriBundle\Entity\Company;
use AgriBundle\Entity\Produit;


use AgriBundle\Form\InterventionType;

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
     * @Route("/intervention/edit/{intervention_id}")
     **/
    public function interventionEditAction($intervention_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($intervention_id == 0){
            $intervention = new Intervention();
            $intervention->parcelles[] = new InterventionParcelle();
            $achat->date = new \Datetime();
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
        return $this->render('AgriBundle:Default:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
