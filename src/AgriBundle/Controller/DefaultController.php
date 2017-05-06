<?php

namespace AgriBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


use AgriBundle\Entity\Achat;
use AgriBundle\Entity\Ilot;
use AgriBundle\Entity\Campagne;
use AgriBundle\Entity\Intervention;
use AgriBundle\Entity\InterventionParcelle;
use AgriBundle\Entity\InterventionProduit;
use AgriBundle\Repository\IlotRepository;
use AgriBundle\Entity\Company;
use AgriBundle\Entity\Produit;
use Datetime;

use AgriBundle\Form\InterventionType;
use AgriBundle\Form\CampagneType;
use AgriBundle\Form\InterventionParcelleType;
use AgriBundle\Form\InterventionProduitType;

class DefaultController extends Controller
{
    private function getCurrentCampagneId($request){
        $session = $request->getSession();
        $campagne_id = $session->get('campagne_id', '');
        if($campagne_id == ''){
            $em = $this->getDoctrine()->getManager();
            $campagne = $em->getRepository('AgriBundle:Campagne')->findAll()[0]->id;
        }
        $new_campagne_id = $request->query->get('campagne_id');
        if($new_campagne_id != ''){
            return $new_campagne_id;
        }

        return $campagne_id;
    }

    private function getCurrentCampagne($request){
        $campagne_id = $this->getCurrentCampagneId($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $em->getRepository('AgriBundle:Campagne')->findOneById($campagne_id);
        if($campagne){
            return $campagne;
        } else {
            return $em->getRepository('AgriBundle:Campagne')->findAll()[0];
        }
    }


    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('AgriBundle:Default:index.html.twig');
    }
    
    /**
     * @Route("/send_file")
     */
    public function sendFileAction()
    {
        return $this->render('AgriBundle:Default:send_file.html.twig');
    }


    /**
     * @Route("/campagnes", name="campagnes")
     */
    public function campagnesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $campagnes = $em->getRepository('AgriBundle:Campagne')->findAll();
        return $this->render('AgriBundle:Default:campagnes.html.twig', array(
            'campagnes2' => $campagnes,
        ));
    }

    /**
     * @Route("/campagne/{campagne_id}", name="campagne")
     **/
    public function campagneEditAction($campagne_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($campagne_id == '0'){
            $campagne = new Campagne();
        } else {
            $campagne = $em->getRepository('AgriBundle:Campagne')->findOneById($campagne_id);
        }
        $form = $this->createForm(CampagneType::class, $campagne);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em->persist($campagne);
            $em->flush();
            return $this->redirectToRoute('campagnes');
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
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
     * @Route("/stocks")
     */
    public function stocksAction()
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('AgriBundle:Produit')
            ->createQueryBuilder('p')
            ->add('orderBy','p.type ASC, p.name ASC')
            ->getQuery()->getResult();

        return $this->render('AgriBundle:Default:stocks.html.twig', array(
            'stocks' => $produits,
        ));
    }

    /**
     * @Route("/achats", name = "achats")
     */
    public function achatsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('file');
            $dir = $this->get('kernel')->getRootDir() . '/../web/uploads/images/';
            $fileName = $file->move($dir, "temp.csv");
            if (($handle = fopen($fileName, "r")) !== FALSE) {
                $i = 0;
                $em->createQuery('DELETE FROM AgriBundle:Achat')->execute();
                while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                    //if ($i == 0) { $i = 1;continue; }
                    $i += 1;
                    $rows = $data;
                    $achat = new Achat();
                    $achat->comment = json_encode($rows);
                    $date = $rows[1];
                    $date = str_replace("/20/","/02/",$date);
                    $date = str_replace("/30/","/03/",$date);
                    $date = str_replace("/40/","/04/",$date);
                    $date = str_replace("/50/","/05/",$date);
                    $date = str_replace("/60/","/06/",$date);
                    $date = str_replace("/70/","/07/",$date);
                    $date = str_replace("/80/","/08/",$date);
                    $date = str_replace("/90/","/09/",$date);
                    $achat->date = date_create_from_format('d/m/Y',$date);
                    $achat->name = $rows[2];
                    $achat->type = $rows[3];
                    $achat->qty = floatval(str_replace(",",".",$rows[4]));
                    $achat->unity = $rows[5];
                    $achat->price = floatval(str_replace(",",".",$rows[6]));
                    $achat->price_total = floatval(str_replace(",",".",$rows[6]));
                    $em->getRepository('AgriBundle:Achat')->add($achat);
                }
                return $this->redirectToRoute('achats');
            }
        }
        $em = $this->getDoctrine()->getManager();

        $achats = $em->getRepository('AgriBundle:Achat')->findAll();

        return $this->render('AgriBundle:Default:achats.html.twig', array(
            'achats' => $achats,
        ));
    }
    /**
     * @Route("parcelles")
     */
    public function parcellesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];

        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagne($campagne);
        foreach ($parcelles as $p) {
            if (!array_key_exists($p->culture, $cultures)) {
                $cultures[$p->culture] = 0;
            }
            $cultures[$p->culture] += $p->surface;
        }
        return $this->render('AgriBundle:Default:parcelles.html.twig', array(
            'campagnes' => $em->getRepository('AgriBundle:Campagne')->findAll(),
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/interventions", name="interventions")
     */
    public function interventions(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $interventions = $em->getRepository('AgriBundle:Intervention')->getAllForCampagne($campagne);
        return $this->render('AgriBundle:Default:interventions.html.twig', array(
            'campagnes' => $em->getRepository('AgriBundle:Campagne')->findAll(),
            'campagne_id' => $campagne->id,
            'interventions' => $interventions,
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}", name="intervention")
     **/
    public function interventionEditAction($intervention_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($intervention_id == 0){
            $intervention = new Intervention();
            $intervention->date = new \Datetime();
            $intervention->type = "phyto";
            $intervention->surface = 0;
            $intervention->campagne = $campagne;
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
            return $this->redirectToRoute('interventions');
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
        $em->getRepository('AgriBundle:Intervention')->delete($intervention_id);
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
            $em->getRepository('AgriBundle:InterventionParcelle')->save($intervention_parcelle);
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
            $intervention_produit = $em->getRepository('AgriBundle:InterventionProduit')->findOneById($intervention_produit_id);
        }
        $form = $this->createForm(InterventionProduitType::class, $intervention_produit);
        $form->handleRequest($request);
        $produits = $em->getRepository('AgriBundle:Produit')->findAll();

        if ($form->isValid()) {
            $em->getRepository('AgriBundle:InterventionProduit')->save($intervention_produit);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('AgriBundle:Default:intervention_produit.html.twig', array(
            'form' => $form->createView(),
            'produits' => $produits
        ));
    }

    /**
     * @Route("api/produit/{produit_name}", name="produit_name")
     **/
    public function produitNameApi($produit_name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('AgriBundle:Produit')->findOneByName($produit_name);

        return $this->json($produit);
    }
}
