<?php

namespace AgriBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Datetime;


use AgriBundle\Controller\CommonController;

use AgriBundle\Entity\Achat;
use AgriBundle\Entity\Campagne;
use AgriBundle\Entity\Culture;
use AgriBundle\Entity\Deplacement;
use AgriBundle\Entity\Gasoil;
use AgriBundle\Entity\Ilot;
use AgriBundle\Entity\Intervention;
use AgriBundle\Entity\InterventionParcelle;
use AgriBundle\Entity\InterventionMateriel;
use AgriBundle\Entity\InterventionProduit;
use AgriBundle\Entity\Livraison;
use AgriBundle\Entity\Materiel;
use AgriBundle\Entity\MaterielEntretien;
use AgriBundle\Entity\Parcelle;
use AgriBundle\Entity\Produit;


use AgriBundle\Form\AchatType;
use AgriBundle\Form\DataType;
use AgriBundle\Form\CampagneType;
use AgriBundle\Form\CompanyType;
use AgriBundle\Form\CultureType;
use AgriBundle\Form\DeplacementType;
use AgriBundle\Form\GasoilType;
use AgriBundle\Form\IlotType;
use AgriBundle\Form\InterventionType;
use AgriBundle\Form\InterventionParcelleType;
use AgriBundle\Form\InterventionMaterielType;
use AgriBundle\Form\InterventionProduitType;
use AgriBundle\Form\LivraisonType;
use AgriBundle\Form\MaterielEntretienType;
use AgriBundle\Form\MaterielType;
use AgriBundle\Form\ParcelleType;
use AgriBundle\Form\ProduitType;


class DefaultController extends CommonController
{

    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        $this->check_user($request);
        return $this->render('AgriBundle:Default:index.html.twig', array(
            'company' => $this->company
        ));
    }

    /**
     * @Route("/profil")
     */
    public function profileAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CompanyType::class, $this->company);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($this->company);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('AgriBundle:Default:profil.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/send_file")
     */
    public function sendFileAction()
    {
        return $this->render('AgriBundle:Default:send_file.html.twig');
    }

    /**
     * @Route("/ilots", name="ilots")
     */
    public function ilotsAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $ilots = $em->getRepository('AgriBundle:Ilot')->getAllforCompany($this->company);
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
            $ilot = $em->getRepository('AgriBundle:Ilot')->findOneById($ilot_id);
            $parcelles = $em->getRepository('AgriBundle:Parcelle')->findByIlot($ilot);
        }
        $form = $this->createForm(IlotType::class, $ilot);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($ilot);
            $em->flush();
            return $this->redirectToRoute('ilots');
        }
        return $this->render('AgriBundle:Default:ilot.html.twig', array(
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

        $campagnes = $em->getRepository('AgriBundle:Campagne')->getAllforCompany($this->company);
        return $this->render('AgriBundle:Default:campagnes.html.twig', array(
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
            $campagne = $em->getRepository('AgriBundle:Campagne')->findOneById($campagne_id);
        }
        $form = $this->createForm(CampagneType::class, $campagne);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($campagne);
            $em->flush();
            return $this->redirectToRoute('campagnes');
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
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

        $cultures = $em->getRepository('AgriBundle:Culture')->getAllforCompany($this->company);
        return $this->render('AgriBundle:Default:cultures.html.twig', array(
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
            $culture = $em->getRepository('AgriBundle:Culture')->findOneById($culture_id);
        }
        $form = $this->createForm(CultureType::class, $culture);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($culture);
            $em->flush();
            return $this->redirectToRoute('cultures');
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
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

        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagne($campagne);
        foreach ($parcelles as $p) {
            if($p->active && $p->culture){
                if (!array_key_exists($p->getCultureName(), $cultures)) {
                    $cultures[$p->getCultureName()] = 0;
                }
                $cultures[$p->getCultureName()] += $p->surface;
                $total += $p->surface;
            }
        }
        return $this->render('AgriBundle:Default:parcelles.html.twig', array(
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
        $ilots = $em->getRepository('AgriBundle:Ilot')->getAllforCompany($this->company);
        $ilots[] = null;
        $cultures = $em->getRepository('AgriBundle:Culture')->getAllforCompany($this->company);
        $cultures[] = null;
        if($parcelle_id == '0'){
            $parcelle = new Parcelle();
            $parcelle->campagne = $campagne;
        } else {
            $parcelle = $em->getRepository('AgriBundle:Parcelle')->findOneById($parcelle_id);
        }
        $form = $this->createForm(ParcelleType::class, $parcelle, array(
            'ilots' => $ilots,
            'cultures' => $cultures
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $parcelle = $em->getRepository('AgriBundle:Parcelle')->save($parcelle);
            return $this->redirectToRoute('parcelles');
        }
        $interventions = [];
        if($parcelle->id != '0'){
            $interventions = $em->getRepository('AgriBundle:Intervention')->getAllForParcelle($parcelle);
        }
        $priceHa = 0;
        foreach($interventions as $it){
            $priceHa += $it->getPriceHa();
        }
        return $this->render('AgriBundle:Default:parcelle.html.twig', array(
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

        $livraisons = $em->getRepository('AgriBundle:Livraison')->getAllForCampagne($campagne);

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
                $parcelles[$livraison->parcelle->id]['ps'] += $livraison->humidite*$livraison->ps;
                $parcelles[$livraison->parcelle->id]['proteine'] += $livraison->humidite*$livraison->proteine;
                $parcelles[$livraison->parcelle->id]['calibrage'] += $livraison->humidite*$livraison->calibrage;
                $parcelles[$livraison->parcelle->id]['impurete'] += $livraison->humidite*$livraison->impurete;
            }
        }
        foreach ($parcelles as $key => $value) {
            $parcelles[$key]['humidite'] = $parcelles[$key]['humidite']/$livraison->poid_norme;
            $parcelles[$key]['ps'] = $parcelles[$key]['ps']/$livraison->poid_norme;
            $parcelles[$key]['proteine'] = $parcelles[$key]['proteine']/$livraison->poid_norme;
            $parcelles[$key]['calibrage'] = $parcelles[$key]['calibrage']/$livraison->poid_norme;
            $parcelles[$key]['impurete'] = $parcelles[$key]['impurete']/$livraison->poid_norme;
            $parcelles[$key]['caracteristiques'] = Livraison::getStaticCarateristiques($parcelles[$key]['humidite']
                , $parcelles[$key]['ps'], $parcelles[$key]['proteine'], $parcelles[$key]['calibrage'], $parcelles[$key]['impurete']);
        }

        //$ecriture = ['operation_id'=>$operation->id,'date'=>$operation->getDateStr(), 'name'=>$operation->name, 'value'=>$operation->getSumEcriture($compte->name)];

        return $this->render('AgriBundle:Default:livraisons.html.twig', array(
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
            $livraison = $em->getRepository('AgriBundle:Livraison')->findOneById($livraison_id);
        }
        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagne($campagne);
        $parcelles[] = null;
        $form = $this->createForm(LivraisonType::class, $livraison, array(
            'parcelles' => $parcelles));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($livraison);
            $em->flush();
            return $this->redirectToRoute('livraisons');
        }
        return $this->render('AgriBundle:Default:livraison.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/parcelle/{parcelle_id}/delete", name="parcelle_delete")
     **/
    public function parcelleDeleteAction($parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AgriBundle:Parcelle')->delete($parcelle_id);
        return $this->redirectToRoute('parcelles');
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
            $intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
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
            $intervention_parcelle->intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_parcelle = $em->getRepository('AgriBundle:InterventionParcelle')->findOneById($intervention_parcelle_id);
        }
        $parcelles =  $em->getRepository('AgriBundle:Parcelle')->getAllForCampagne($campagne);
        $form = $this->createForm(InterventionParcelleType::class, $intervention_parcelle, array(
            'parcelles' => $parcelles
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->getRepository('AgriBundle:InterventionParcelle')->save($intervention_parcelle);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}/parcelle/{intervention_parcelle_id}/delete", name="intervention_parcelle_delete")
     **/
    public function interventionParcelleDeleteAction($intervention_id, $intervention_parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AgriBundle:InterventionParcelle')->delete($intervention_parcelle_id);
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
            $intervention_produit->intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_produit = $em->getRepository('AgriBundle:InterventionProduit')->findOneById($intervention_produit_id);
        }
        $form = $this->createForm(InterventionProduitType::class, $intervention_produit);
        $form->handleRequest($request);
        $produits = $em->getRepository('AgriBundle:Produit')->getAllName($campagne);

        if ($form->isSubmitted()) {
            $em->getRepository('AgriBundle:InterventionProduit')->save($intervention_produit, $campagne);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('AgriBundle:Default:intervention_produit.html.twig', array(
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
            $intervention_materiel->intervention = $em->getRepository('AgriBundle:Intervention')->findOneById($intervention_id);
        } else {
            $intervention_materiel = $em->getRepository('AgriBundle:InterventionMateriel')->findOneById($intervention_materiel);
        }
        $materiels =  $em->getRepository('AgriBundle:Materiel')->getAllForCompany($this->company);
        $form = $this->createForm(InterventionMaterielType::class, $intervention_materiel, array(
            'materiels' => $materiels
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->getRepository('AgriBundle:InterventionMateriel')->save($intervention_materiel);
            return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/intervention/{intervention_id}/produit/{intervention_produit_id}/delete", name="intervention_produit_delete")
     **/
    public function interventionProduitDeleteAction($intervention_id, $intervention_produit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AgriBundle:InterventionProduit')->delete($intervention_produit_id);
        return $this->redirectToRoute('intervention', array('intervention_id' => $intervention_id));
    }

    /**
     * @Route("/calendar", name="calendar")
     **/
    public function calendar(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $interventions = $em->getRepository('AgriBundle:Intervention')->getAllForCompany($this->company);
        $gasoils = $em->getRepository('AgriBundle:Gasoil')->getAllForCompany($this->company);
        $deplacements = $em->getRepository('AgriBundle:Deplacement')->getAllForCompany($this->company);
        return $this->render('AgriBundle:Default:calendar.html.twig', array(
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

        $materiels = $em->getRepository('AgriBundle:Materiel')->getAllForCompany($this->company);

        return $this->render('AgriBundle:Default:materiels.html.twig', array(
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
            $materiel = $em->getRepository('AgriBundle:Materiel')->findOneById($materiel_id);
            $entretiens =  $em->getRepository('AgriBundle:MaterielEntretien')->findByMateriel($materiel);
            $interventions =  $em->getRepository('AgriBundle:Intervention')->getAllForMateriel($materiel);
        }
        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($materiel);
            $em->flush();
            return $this->redirectToRoute('materiels');
        }
        return $this->render('AgriBundle:Default:materiel.html.twig', array(
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
            $entretien->materiel = $em->getRepository('AgriBundle:Materiel')->findOneById($materiel_id);
            $entretien->date = new \Datetime();
        } else {
            $entretien = $em->getRepository('AgriBundle:MaterielEntretien')->findOneById($entretien_id);
        }
        $form = $this->createForm(MaterielEntretienType::class, $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($entretien);
            $em->flush();
            return $this->redirectToRoute('materiel', array('materiel_id' => $materiel_id));
        }
        return $this->render('AgriBundle:Default:materiel_entretien.html.twig', array(
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

        $deplacements = $em->getRepository('AgriBundle:Deplacement')->getAllForCampagne($campagne);
        return $this->render('AgriBundle:Default:deplacements.html.twig', array(
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
            $deplacement = $em->getRepository('AgriBundle:Deplacement')->findOneById($deplacement_id);
        }
        $form = $this->createForm(DeplacementType::class, $deplacement);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $gasoil = $em->getRepository('AgriBundle:Deplacement')->save($deplacement);
            return $this->redirectToRoute('deplacements');
        }
        return $this->render('AgriBundle:Default:deplacement.html.twig', array(
            'form' => $form->createView(),
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }
}
