<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Datetime;


use AppBundle\Controller\CommonController;

use AppBundle\Entity\Achat;
use AppBundle\Entity\Campagne;
use AppBundle\Entity\Culture;
use AppBundle\Entity\Deplacement;
use AppBundle\Entity\Gasoil;
use AppBundle\Entity\Ilot;
use AppBundle\Entity\Intervention;
use AppBundle\Entity\InterventionParcelle;
use AppBundle\Entity\InterventionMateriel;
use AppBundle\Entity\InterventionProduit;
use AppBundle\Entity\Livraison;
use AppBundle\Entity\Materiel;
use AppBundle\Entity\MaterielEntretien;
use AppBundle\Entity\Parcelle;
use AppBundle\Entity\Produit;


use AppBundle\Form\AchatType;
use AppBundle\Form\DataType;
use AppBundle\Form\CampagneType;
use AppBundle\Form\CompanyType;
use AppBundle\Form\CultureType;
use AppBundle\Form\DeplacementType;
use AppBundle\Form\GasoilType;
use AppBundle\Form\IlotType;
use AppBundle\Form\InterventionType;
use AppBundle\Form\InterventionParcelleType;
use AppBundle\Form\InterventionMaterielType;
use AppBundle\Form\InterventionProduitType;
use AppBundle\Form\LivraisonType;
use AppBundle\Form\MaterielEntretienType;
use AppBundle\Form\MaterielType;
use AppBundle\Form\ParcelleType;
use AppBundle\Form\ProduitType;


class DefaultController extends CommonController
{

    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        $this->check_user($request);
        return $this->render('Default/index.html.twig', array(
            'company' => $this->company
        ));
    }

    /**
     * @Route("/send_file")
     */
    public function sendFileAction()
    {
        return $this->render('Default/send_file.html.twig');
    }

    /**
     * @Route("/ilots", name="ilots")
     */
    public function ilotsAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $ilots = $em->getRepository('AppBundle:Ilot')->getAllforCompany($this->company);
        $sum_ilots = array_reduce($ilots, function($i, $obj)
        {
            return $i += $obj->surface;
        });

        return $this->render('Default/ilots.html.twig', array(
            'ilots' => $ilots,
            'sum_ilots' => $sum_ilots,
        ));
    }

    /**
     * @Route("/ilot/{ilot_id}", name="ilot")
     **/
    public function ilotEditAction($ilot_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $parcelles = [];
        if($ilot_id == '0'){
            $ilot = new Ilot();
            $ilot->company = $this->company;
        } else {
            $ilot = $em->getRepository('AppBundle:Ilot')->findOneById($ilot_id);
            $parcelles = $em->getRepository('AppBundle:Parcelle')->findByIlot($ilot);
        }
        $form = $this->createForm(IlotType::class, $ilot);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($ilot);
            $em->flush();
            return $this->redirectToRoute('ilots');
        }
        return $this->render('Default/ilot.html.twig', array(
            'form' => $form->createView(),
            'parcelles' => $parcelles
        ));
    }


    /**
     * @Route("/campagnes", name="campagnes")
     */
    public function campagnesAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $campagnes = $em->getRepository('AppBundle:Campagne')->getAllforCompany($this->company);
        return $this->render('Default/campagnes.html.twig', array(
            'campagnes2' => $campagnes,
        ));
    }

    /**
     * @Route("/campagne/{campagne_id}", name="campagne")
     **/
    public function campagneEditAction($campagne_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        if($campagne_id == '0'){
            $campagne = new Campagne();
            $campagne->company = $this->company;
        } else {
            $campagne = $em->getRepository('AppBundle:Campagne')->findOneById($campagne_id);
        }
        $form = $this->createForm(CampagneType::class, $campagne);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($campagne);
            $em->flush();
            return $this->redirectToRoute('campagnes');
        }
        return $this->render('AppBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/cultures", name="cultures")
     */
    public function culturesAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $cultures = $em->getRepository('AppBundle:Culture')->getAllforCompany($this->company);
        return $this->render('Default/cultures.html.twig', array(
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/culture/{culture_id}", name="culture")
     **/
    public function cultureEditAction($culture_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        if($culture_id == '0'){
            $culture = new Culture();
            $culture->company = $this->company;
        } else {
            $culture = $em->getRepository('AppBundle:Culture')->findOneById($culture_id);
        }
        $form = $this->createForm(CultureType::class, $culture);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($culture);
            $em->flush();
            return $this->redirectToRoute('cultures');
        }
        return $this->render('AppBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("parcelles", name="parcelles")
     */
    public function parcellesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];
        $total = 0;

        $parcelles = $em->getRepository('AppBundle:Parcelle')->getAllForCampagne($campagne);
        foreach ($parcelles as $p) {
            if($p->active && $p->culture){
                if (!array_key_exists($p->getCultureName(), $cultures)) {
                    $cultures[$p->getCultureName()] = 0;
                }
                $cultures[$p->getCultureName()] += $p->surface;
                $total += $p->surface;
            }
        }
        return $this->render('Default/parcelles.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
            'total' => $total
        ));
    }

    /**
     * @Route("/parcelle/{parcelle_id}", name="parcelle")
     **/
    public function parcelleEditAction($parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $ilots = $em->getRepository('AppBundle:Ilot')->getAllforCompany($this->company);
        $ilots[] = null;
        $cultures = $em->getRepository('AppBundle:Culture')->getAllforCompany($this->company);
        $cultures[] = null;
        if($parcelle_id == '0'){
            $parcelle = new Parcelle();
            $parcelle->campagne = $campagne;
        } else {
            $parcelle = $em->getRepository('AppBundle:Parcelle')->findOneById($parcelle_id);
        }
        $form = $this->createForm(ParcelleType::class, $parcelle, array(
            'ilots' => $ilots,
            'cultures' => $cultures
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $parcelle = $em->getRepository('AppBundle:Parcelle')->save($parcelle);
            return $this->redirectToRoute('parcelles');
        }
        $interventions = [];
        if($parcelle->id != '0'){
            $interventions = $em->getRepository('AppBundle:Intervention')->getAllForParcelle($parcelle);
        }
        $priceHa = 0;
        foreach($interventions as $it){
            $priceHa += $it->getPriceHa();
        }
        return $this->render('Default/parcelle.html.twig', array(
            'form' => $form->createView(),
            'interventions' => $interventions,
            'priceHa' => $priceHa
        ));
    }

    /**
     * @Route("livraisons", name="livraisons")
     */
    public function livraisonsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];
        $total = 0;

        $livraisons = $em->getRepository('AppBundle:Livraison')->getAllForCampagne($campagne);

        $cultures = [];
        foreach ($livraisons as $livraison) {
            if (!array_key_exists($livraison->espece, $cultures)) {
                $cultures[$livraison->espece] = 0;
            }
            $cultures[$livraison->espece] += $livraison->poid_norme;
        }

        $parcelles = [];
        foreach ($livraisons as $livraison) {
            if($livraison->parcelle){
                if (!array_key_exists($livraison->parcelle->id, $parcelles)) {
                    $parcelles[$livraison->parcelle->id] = ['name'=>$livraison->parcelle->completeName, 'espece' => $livraison->espece, 'surface'=>$livraison->parcelle->surface
                    , 'poid' => 0, 'humidite' => 0, 'ps' => 0, 'proteine' => 0, 'calibrage' => 0, 'impurete' => 0];
                }
                $parcelles[$livraison->parcelle->id]['poid'] += $livraison->poid_norme;
                $parcelles[$livraison->parcelle->id]['humidite'] += $livraison->humidite*$livraison->poid_norme;
                $parcelles[$livraison->parcelle->id]['ps'] += $livraison->ps*$livraison->poid_norme;
                $parcelles[$livraison->parcelle->id]['proteine'] += $livraison->proteine*$livraison->poid_norme;
                $parcelles[$livraison->parcelle->id]['calibrage'] += $livraison->calibrage*$livraison->poid_norme;
                $parcelles[$livraison->parcelle->id]['impurete'] += $livraison->impurete*$livraison->poid_norme;
            }
        }
        foreach ($parcelles as $key => $value) {
            $parcelles[$key]['humidite'] = $parcelles[$key]['humidite']/$parcelles[$key]['poid'];
            $parcelles[$key]['ps'] = $parcelles[$key]['ps']/$parcelles[$key]['poid'];
            $parcelles[$key]['proteine'] = $parcelles[$key]['proteine']/$parcelles[$key]['poid'];
            $parcelles[$key]['calibrage'] = $parcelles[$key]['calibrage']/$parcelles[$key]['poid'];
            $parcelles[$key]['impurete'] = $parcelles[$key]['impurete']/$parcelles[$key]['poid'];
            $parcelles[$key]['caracteristiques'] = Livraison::getStaticCarateristiques($parcelles[$key]['humidite']
                , $parcelles[$key]['ps'], $parcelles[$key]['proteine'], $parcelles[$key]['calibrage'], $parcelles[$key]['impurete']);
        }

        //$ecriture = ['operation_id'=>$operation->id,'date'=>$operation->getDateStr(), 'name'=>$operation->name, 'value'=>$operation->getSumEcriture($compte->name)];

        return $this->render('Default/livraisons.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'livraisons' => $livraisons,
            'cultures' => $cultures,
            'parcelles' => $parcelles
        ));
    }

    /**
     * @Route("/livraison/{livraison_id}", name="livraison")
     **/
    public function livraisonEditAction($livraison_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($livraison_id == '0'){
            $livraison = new Livraison();
            $livraison->date = new \Datetime();
            $livraison->campagne = $campagne;
        } else {
            $livraison = $em->getRepository('AppBundle:Livraison')->findOneById($livraison_id);
        }
        $parcelles = $em->getRepository('AppBundle:Parcelle')->getAllForCampagne($campagne);
        $parcelles[] = null;
        $form = $this->createForm(LivraisonType::class, $livraison, array(
            'parcelles' => $parcelles));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($livraison);
            $em->flush();
            return $this->redirectToRoute('livraisons');
        }
        return $this->render('Default/livraison.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/parcelle/{parcelle_id}/delete", name="parcelle_delete")
     **/
    public function parcelleDeleteAction($parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AppBundle:Parcelle')->delete($parcelle_id);
        return $this->redirectToRoute('parcelles');
    }

    /**
     * @Route("/interventions", name="interventions")
     */
    public function interventions(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $interventions = $em->getRepository('AppBundle:Intervention')->getAllForCampagne($campagne);
        return $this->render('Default/interventions.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'interventions' => $interventions,
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}", name="intervention")
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
            $intervention = $em->getRepository('AppBundle:Intervention')->findOneById($intervention_id);
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
        $em->getRepository('AppBundle:Intervention')->delete($intervention_id);
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
            $intervention_parcelle->intervention = $em->getRepository('AppBundle:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_parcelle = $em->getRepository('AppBundle:InterventionParcelle')->findOneById($intervention_parcelle_id);
        }
        $parcelles =  $em->getRepository('AppBundle:Parcelle')->getAllForCampagne($campagne);
        $form = $this->createForm(InterventionParcelleType::class, $intervention_parcelle, array(
            'parcelles' => $parcelles
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->getRepository('AppBundle:InterventionParcelle')->save($intervention_parcelle);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('AppBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}/parcelle/{intervention_parcelle_id}/delete", name="intervention_parcelle_delete")
     **/
    public function interventionParcelleDeleteAction($intervention_id, $intervention_parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AppBundle:InterventionParcelle')->delete($intervention_parcelle_id);
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
            $intervention_produit->intervention = $em->getRepository('AppBundle:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_produit = $em->getRepository('AppBundle:InterventionProduit')->findOneById($intervention_produit_id);
        }
        $form = $this->createForm(InterventionProduitType::class, $intervention_produit);
        $form->handleRequest($request);
        $produits = $em->getRepository('AppBundle:Produit')->getAllName($campagne);

        if ($form->isSubmitted()) {
            $em->getRepository('AppBundle:InterventionProduit')->save($intervention_produit, $campagne);
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
            $intervention_materiel->intervention = $em->getRepository('AppBundle:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_materiel = $em->getRepository('AppBundle:InterventionMateriel')->findOneById($intervention_materiel);
        }
        $materiels =  $em->getRepository('AppBundle:Materiel')->getAllForCompany($this->company);
        $form = $this->createForm(InterventionMaterielType::class, $intervention_materiel, array(
            'materiels' => $materiels
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->getRepository('AppBundle:InterventionMateriel')->save($intervention_materiel);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('AppBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}/produit/{intervention_produit_id}/delete", name="intervention_produit_delete")
     **/
    public function interventionProduitDeleteAction($intervention_id, $intervention_produit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AppBundle:InterventionProduit')->delete($intervention_produit_id);
        return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
    }

    /**
     * @Route("/calendar", name="calendar")
     **/
    public function calendar(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $interventions = $em->getRepository('AppBundle:Intervention')->getAllForCompany($this->company);
        $gasoils = $em->getRepository('AppBundle:Gasoil')->getAllForCompany($this->company);
        $deplacements = $em->getRepository('AppBundle:Deplacement')->getAllForCompany($this->company);
        return $this->render('Default/calendar.html.twig', array(
            'interventions' => $interventions,
            'gasoils' => $gasoils,
            'deplacements' => $deplacements
        ));
    }

    /**
     * @Route("/materiels", name="materiels")
     */
    public function materielsAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $materiels = $em->getRepository('AppBundle:Materiel')->getAllForCompany($this->company);

        return $this->render('Default/materiels.html.twig', array(
            'materiels' => $materiels,
        ));
    }

    /**
     * @Route("/materiel/{materiel_id}", name="materiel")
     **/
    public function materielEditAction($materiel_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $entretiens = [];
        $interventions = [];
        if($materiel_id == '0'){
            $materiel = new Materiel();
            $materiel->company = $this->company;
        } else {
            $materiel = $em->getRepository('AppBundle:Materiel')->findOneById($materiel_id);
            $entretiens =  $em->getRepository('AppBundle:MaterielEntretien')->findByMateriel($materiel);
            $interventions =  $em->getRepository('AppBundle:Intervention')->getAllForMateriel($materiel);
        }
        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($materiel);
            $em->flush();
            return $this->redirectToRoute('materiels');
        }
        return $this->render('Default/materiel.html.twig', array(
            'form' => $form->createView(),
            'materiel' => $materiel,
            'entretiens' => $entretiens,
            'interventions' => $interventions,
        ));
    }

    /**
     * @Route("/materiel/{materiel_id}/entretien/{entretien_id}", name="entretien_materiel")
     **/
    public function entretienMaterielAction($materiel_id, $entretien_id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        if($entretien_id == '0'){
            $entretien = new MaterielEntretien();
            $entretien->company = $this->company;
            $entretien->materiel = $em->getRepository('AppBundle:Materiel')->findOneById($materiel_id);
            $entretien->date = new \Datetime();
        } else {
            $entretien = $em->getRepository('AppBundle:MaterielEntretien')->findOneById($entretien_id);
        }
        $form = $this->createForm(MaterielEntretienType::class, $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($entretien);
            $em->flush();
            return $this->redirectToRoute('materiel', array('materiel_id' => $materiel_id));
        }
        return $this->render('Default/materiel_entretien.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("deplacements", name="deplacements")
     */
    public function deplacementsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $deplacements = $em->getRepository('AppBundle:Deplacement')->getAllForCampagne($campagne);
        return $this->render('Default/deplacements.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'deplacements' => $deplacements
        ));
    }

    /**
     * @Route("/deplacement/{deplacement_id}", name="deplacement")
     **/
    public function deplacementEditAction($deplacement_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($deplacement_id == '0'){
            $deplacement = new Deplacement();
            $deplacement->name = "Warmo";
            $deplacement->km = 164;
            $deplacement->campagne = $campagne;
            $deplacement->date = new \DateTime();
        } else {
            $deplacement = $em->getRepository('AppBundle:Deplacement')->findOneById($deplacement_id);
        }
        $form = $this->createForm(DeplacementType::class, $deplacement);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $gasoil = $em->getRepository('AppBundle:Deplacement')->save($deplacement);
            return $this->redirectToRoute('deplacements');
        }
        return $this->render('Default/deplacement.html.twig', array(
            'form' => $form->createView(),
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }
}
