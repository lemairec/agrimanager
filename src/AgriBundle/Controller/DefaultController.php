<?php

namespace AgriBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AgriBundle\Entity\Ilot;
use AgriBundle\Repository\IlotRepository;
use AgriBundle\Entity\Company;

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
     * @Route("/interventions/{campagne_id}")
     */
    public function interventions($campagne_id)
    {
        $em = $this->getDoctrine()->getManager();
        $interventions = $em->getRepository('AgriBundle:Intervention')->getAll();
        foreach ($interventions as $i) {
            echo("toto");
            echo(json_encode($i->parcelles->toArray()));
        }
        return $this->render('AgriBundle:Default:interventions.html.twig', array(
                    'interventions' => $interventions,
                        ));
    }

    /**
     * @Route("/init")
     */
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager();
        $company = new Company();
        $company->name = "warmo";
        $company->adresse = "12 route";
        $em->persist($company);
        $compagny_id = $company->id;
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("cote merlan", 5.54);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-cote-merlan", "orge", 5.54);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("les holles galant", 3);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-holles-galant", "orge", 3);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("la noue balinet", 9.68);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-noue-balinet", "orge", $ilot->surface);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("chemin des canons", 5.68);
        $parcelle1 = $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "colza-chemin-canons", "colza", $ilot->surface);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("chemin du mesnil", 32.94);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-bettrave", "orge", 7.22);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-ble", "orge", 2.93);
        $parcelle2 = $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "colza", "colza", 14.42);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "ble-colza", "ble", 7.22);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "ble-colza-bande", "ble", 1.15);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("batterie moucherie", 19.6);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "ble-pdt", "ble", 4.5);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "ble-bettrave", "ble", 15.1);
        $parcelles =[$parcelle1, $parcelle2];
        $em->getRepository('AgriBundle:Intervention')->add("semis", "2016-08-01", $parcelles);
        $em->persist($ilot);
        $em->flush();
        return $this->redirect('/');
    }
}
