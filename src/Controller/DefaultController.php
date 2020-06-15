<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Achat;
use App\Entity\Campagne;
use App\Entity\Culture;
use App\Entity\Deplacement;
use App\Entity\Gasoil;
use App\Entity\Ilot;
use App\Entity\Intervention;
use App\Entity\InterventionParcelle;
use App\Entity\InterventionMateriel;
use App\Entity\InterventionProduit;
use App\Entity\Materiel;
use App\Entity\MaterielEntretien;
use App\Entity\Parcelle;
use App\Entity\Produit;
use App\Entity\Variete;


use App\Form\AchatType;
use App\Form\DataType;
use App\Form\CampagneType;
use App\Form\CompanyType;
use App\Form\CultureType;
use App\Form\DeplacementType;
use App\Form\GasoilType;
use App\Form\IlotType;
use App\Form\InterventionType;
use App\Form\InterventionParcelleType;
use App\Form\InterventionMaterielType;
use App\Form\InterventionProduitType;
use App\Form\LivraisonType;
use App\Form\MaterielEntretienType;
use App\Form\MaterielType;
use App\Form\ParcelleType;
use App\Form\ProduitType;
use App\Form\VarieteType;


class DefaultController extends CommonController
{

    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return  $this->render('home.html.twig');
        }
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $interventions = $em->getRepository('App:Intervention')->getLast5ForCampagne($campagne);
        $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagne($campagne);

        $cultures = [];
        $total = 0;
        foreach ($parcelles as $p) {
            if($p->active && $p->culture){
                if (!array_key_exists($p->getCultureName(), $cultures)) {
                    $cultures[$p->getCultureName()] = ['color'=> $p->culture->color, 'surface'=> 0];
                }
                $cultures[$p->getCultureName()]['surface'] += $p->surface;
                $total += $p->surface;
            }
        }

        $this->mylog("Accès au site");

        $meteoCity = "Paris";
        if($this->company->meteoCity){
            $meteoCity = $this->company->meteoCity;
        }

        return $this->render('Default/home_connected.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'interventions' => $interventions,
            'parcelles' => $parcelles,
            'meteoCity' => $meteoCity,
            'company' => $this->company,
            'user' => $this->getUser(),
            'surfaceTotale' => $total,
            'cultures' => $cultures
        ));
    }

    /**
     * @Route("/my-error", name="my-error")
     */
    public function myErrorAction(Request $request)
    {
    }


    /**
     * @Route("test_mail", name="test_mail")
     */
    public function testMail(Request $request,  \Swift_Mailer $mailer)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $this->sendMail("noreply@maplaine.fr", 'lemairec02@gmail.com', "Test", $mailer);

        return  $this->render('home.html.twig');
    }

    /**
     * @Route("/profile/historique", name="profile_historique")
     */
    public function profile(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $logs = $em->getRepository('App:Log')->find10ByUser($this->getUser());
        return $this->render('Profile/historique.html.twig', array(
            'logs' => $logs,
            'navs' => ["historique" => "profile_historique"]
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

        $ilots = $em->getRepository('App:Ilot')->getAllforCompany($this->company);
        $sum_ilots = array_reduce($ilots, function($i, $obj)
        {
            return $i += $obj->surface;
        });

        return $this->render('Default/ilots.html.twig', array(
            'ilots' => $ilots,
            'sum_ilots' => $sum_ilots,
            'navs' => ["Ilots" => "ilots"]
        ));
    }

    /**
     * @Route("/assolement2", name="assolement2")
     */
    public function bilanIlotsAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $ilots = $em->getRepository('App:Ilot')->getAllforCompany($this->company);

        $campagnes = $em->getRepository('App:Campagne')->getAllforCompany($this->company);
        
        $res = [];
        foreach($ilots as $i){
            $ligne = ["ilot" => $i];
            foreach($campagnes as $c){
                $ligne[$c->name] = ["parcelles" => $em->getRepository('App:Parcelle')->getAllForIlotCampagne($i, $c)];
            }
            $res[] = $ligne;
        }

        $cultures = $em->getRepository('App:Culture')->getAllforCompany($this->company);
        $cultures_res = [];
        foreach($cultures as $c2){
            $ligne = ["culture" => $c2];
            foreach($campagnes as $c){
                $ligne[$c->name] = ["sum" => $em->getRepository('App:Parcelle')->getSumForCultureCampagne($c2, $c)];
            }
            $cultures_res[] = $ligne;
        }
        //dump($res);
       

        return $this->render('Default/assolement.html.twig', array(
            'ilots' => $res,
            'cultures' => $cultures_res,
            'campagnes2' => $campagnes,
            'navs' => ["Ilots" => "ilots"]
        ));
    }

    /**
     * @Route("/assolement", name="assolement")
     */
    public function bilanAssolement2Action(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $ilots = $em->getRepository('App:Ilot')->getAllforCompany($this->company);

        $campagnes = $em->getRepository('App:Campagne')->getAllforCompany($this->company);
        
        $res = [];
        foreach($ilots as $i){
            $maxParcelles = 0;
            foreach($campagnes as $c){
                $c = count($em->getRepository('App:Parcelle')->getAllForIlotCampagne($i, $c));
                if($c>$maxParcelles){
                    $maxParcelles = $c;
                }
            }
            $ligne_ilot = ["ilot"=>$i, "parcelles"=>[], "parcelles_count"=>$maxParcelles];
            for($j = 0; $j < $maxParcelles; $j=$j+1){
                $ligne = ["ilot" => $i, "ilot_name" => $i->name."_".$j, "idx" => $j];
                foreach($campagnes as $c){
                    foreach($campagnes as $c){
                        $parcelles = $em->getRepository('App:Parcelle')->getAllForIlotCampagne($i, $c);
                        if($j<count($parcelles)){
                            $ligne[$c->name] = ["name" => $parcelles[$j]->name, "surface" => $parcelles[$j]->surface, "culture" => $parcelles[$j]->culture];
                        } else {
                            $ligne[$c->name] = ["name" => "", "surface" => 0,"culture" => ""];
                        }
                    }
                    $cultures_res[] = $ligne;
                }
                $ligne_ilot["parcelles"][] = $ligne;
                
            }
            $res[] = $ligne_ilot;
            
        }

        $cultures = $em->getRepository('App:Culture')->getAllforCompany($this->company);
        $cultures_res = [];
        foreach($cultures as $c2){
            $ligne = ["culture" => $c2];
            foreach($campagnes as $c){
                $ligne[$c->name] = ["sum" => $em->getRepository('App:Parcelle')->getSumForCultureCampagne($c2, $c)];
            }
            $cultures_res[] = $ligne;
        }
        //dump($res);
       

        return $this->render('Default/assolement2.html.twig', array(
            'ilots' => $res,
            'cultures' => $cultures_res,
            'campagnes2' => $campagnes,
            'navs' => ["Ilots" => "ilots"]
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
            $ilot = $em->getRepository('App:Ilot')->findOneById($ilot_id);
            $parcelles = $em->getRepository('App:Parcelle')->getAllForIlot($ilot);
        }
        $form = $this->createForm(IlotType::class, $ilot);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            if($ilot_id == '0'){
                $this->mylog("Création de l'ilot : ".$ilot);
            } else {
                $this->mylog("Modification de l'ilot : ".$ilot);
            }
            $em->persist($ilot);
            $em->flush();
            return $this->redirectToRoute('ilots');
        }
        return $this->render('Default/ilot.html.twig', array(
            'form' => $form->createView(),
            'parcelles' => $parcelles,
            'navs' => ["Ilots" => "ilots"]
        ));
    }


    /**
     * @Route("/campagnes", name="campagnes")
     */
    public function campagnesAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $campagnes = $em->getRepository('App:Campagne')->getAllforCompany($this->company);
        return $this->render('Default/campagnes.html.twig', array(
            'campagnes2' => $campagnes,
            'navs' => ["Campagnes" => "campagnes"]
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
            $campagne = $em->getRepository('App:Campagne')->findOneById($campagne_id);
        }
        $form = $this->createForm(CampagneType::class, $campagne);
        if($this->getUser()->getUsername() == "lejard"){
            $form->add('commercialisation');
        }
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($campagne);
            $em->flush();
            return $this->redirectToRoute('campagnes');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
            'navs' => ["Campagnes" => "campagnes"]
        ));
    }

    /**
     * @Route("/cultures", name="cultures")
     */
    public function culturesAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $cultures = $em->getRepository('App:Culture')->getAllforCompany($this->company);
        return $this->render('Default/cultures.html.twig', array(
            'cultures' => $cultures,
            'navs' => ["Cultures" => "cultures"]
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
            $culture = $em->getRepository('App:Culture')->findOneById($culture_id);
        }
        $form = $this->createForm(CultureType::class, $culture);
        if($this->getUser()->getUsername() == "lejard"){
            $form->add('commercialisation');
            $form->add('rendementObj');
            $form->add('prixObj');
        }
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $pos = strpos($culture->color, "#");
            if ($pos === false) {
                $culture->color = "#".$culture->color;
            }
            if($culture_id == '0'){
                $this->mylog("Création de la culture : ".$culture);
            } else {
                $this->mylog("Modification de la culture : ".$culture);
            }

            $em->persist($culture);
            $em->flush();
            return $this->redirectToRoute('cultures');
        }
        return $this->render('Default/culture.html.twig', array(
            'form' => $form->createView(),
            'navs' => ["Cultures" => "cultures"]
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

        $is = $em->getRepository('App:Ilot')->getAllForCompany($campagne->company);
        $ilots = [];
        foreach ($is as $i) {
            $ilots[] = ["id" => $i->id, "name" => $i->name, "surface_totale" => $i->surface, "surface" => 0];
        }

        $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagne($campagne);
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
        }

        $ilots2 = [];
        foreach ($ilots as $ilot){
            if($ilot["surface_totale"] > $ilot["surface"]){
                $ilot["surface_restante"] = $ilot["surface_totale"]-$ilot["surface"];
                $ilots2[] = $ilot;
            }
        }

        return $this->render('Default/parcelles.html.twig', array(
            'ilots' => $ilots2,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
            'total' => $total,
            'navs' => ["Parcelles" => "parcelles"]
        ));
    }

    /**
     * @Route("/parcelle/{parcelle_id}", name="parcelle")
     **/
    public function parcelleEditAction($parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $ilots = $em->getRepository('App:Ilot')->getAllforCompany($this->company);
        $ilots[] = null;
        $cultures = $em->getRepository('App:Culture')->getAllforCompany($this->company);
        $cultures[] = null;
        if($parcelle_id == '0'){
            $parcelle = new Parcelle();
            $parcelle->active = 1;
            $parcelle->campagne = $campagne;
            $parcelle->surface = $request->query->get("surface", 0);
            $parcelle->ilot = $em->getRepository('App:Ilot')->find($request->query->get("ilot_id", ""));
            $parcelle->geoJson = "";
        } else {
            $parcelle = $em->getRepository('App:Parcelle')->findOneById($parcelle_id);
        }

        //dump($parcelle);
        $form = $this->createForm(ParcelleType::class, $parcelle, array(
            'ilots' => $ilots,
            'cultures' => $cultures
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $parcelle = $em->getRepository('App:Parcelle')->save($parcelle);
            return $this->redirectToRoute('parcelles');
        }
        $interventions = [];
        if($parcelle->id && $parcelle->id != '0'){
            $interventions = $em->getRepository('App:Intervention')->getAllForParcelle($parcelle);
         }
        $priceHa = 0;
        foreach($interventions as $it){
            $priceHa += $it->getPriceHa();
        }
        return $this->render('Default/parcelle.html.twig', array(
            'form' => $form->createView(),
            'parcelle_id' => $parcelle_id,
            'parcelle' => $parcelle,
            'interventions' => $interventions,
            'priceHa' => $priceHa,
            'navs' => ["Parcelles" => "parcelles"]
        ));
    }

    /**
     * @Route("/parcelle/{parcelle_id}/variete/{variete_id}", name="variete")
     **/
    public function varieteEditAction($parcelle_id, $variete_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $parcelle = $em->getRepository('App:Parcelle')->findOneById($parcelle_id);
        $cultures = $em->getRepository('App:Culture')->getAllforCompany($this->company);
        if($variete_id == '0'){
            $variete = new Variete();
            $variete->parcelle = $parcelle;
            $variete->ordre = 0;

            $sum = 0;
            foreach($parcelle->varietes as $v){
                $sum += $v->surface;
            }
            $variete->surface = $parcelle->surface-$sum;
        } else {
            $variete = $em->getRepository('App:Variete')->findOneById($variete_id);
        }

        $form = $this->createForm(VarieteType::class, $variete, array(
            'cultures' => $cultures
        ));

        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($variete);
            $em->flush();
            return $this->redirectToRoute('parcelle',['parcelle_id'=> $parcelle_id]);
        }

        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
            'navs' => ["Campagnes" => "campagnes"]
        ));

    }

    /**
     * @Route("/parcelle/{parcelle_id}/variete/{variete_id}/delete", name="variete_delete")
     **/
    public function varieteDeleteAction($parcelle_id, $variete_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $parcelle = $em->getRepository('App:Parcelle')->findOneById($parcelle_id);
        $variete = $em->getRepository('App:Variete')->findOneById($variete_id);
        
        $em->remove($variete);
        $em->flush();
        return $this->redirectToRoute('parcelle',['parcelle_id'=> $parcelle_id]);

    }

    /**
     * @Route("/parcelle/{parcelle_id}/delete", name="parcelle_delete")
     **/
    public function parcelleDeleteAction($parcelle_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:Parcelle')->delete($parcelle_id);
        return $this->redirectToRoute('parcelles');
    }

    /**
     * @Route("/calendar", name="calendar")
     **/
    public function calendar(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $interventions = $em->getRepository('App:Intervention')->getAllForCompany($this->company);
        $gasoils = $em->getRepository('App:Gasoil')->getAllForCompany($this->company);
        $deplacements = $em->getRepository('App:Deplacement')->getAllForCompany($this->company);
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

        $materiels = $em->getRepository('App:Materiel')->getAllForCompany($this->company);

        return $this->render('Default/materiels.html.twig', array(
            'materiels' => $materiels,
            'navs' => ["Materiels" => "materiels"]
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
            $materiel = $em->getRepository('App:Materiel')->findOneById($materiel_id);
            $entretiens =  $em->getRepository('App:MaterielEntretien')->findByMateriel($materiel);
            $interventions =  $em->getRepository('App:Intervention')->getAllForMateriel($materiel);
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
            'navs' => ["Materiels" => "materiels"]
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
            $entretien->materiel = $em->getRepository('App:Materiel')->findOneById($materiel_id);
            $entretien->date = new \Datetime();
        } else {
            $entretien = $em->getRepository('App:MaterielEntretien')->findOneById($entretien_id);
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

        $deplacements = $em->getRepository('App:Deplacement')->getAllForCampagne($campagne);
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
            $deplacement = $em->getRepository('App:Deplacement')->findOneById($deplacement_id);
        }
        $form = $this->createForm(DeplacementType::class, $deplacement);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $gasoil = $em->getRepository('App:Deplacement')->save($deplacement);
            return $this->redirectToRoute('deplacements');
        }
        return $this->render('Default/deplacement.html.twig', array(
            'form' => $form->createView(),
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }

    /**
     * @Route("/carte", name="carte")
     **/
    public function carte(Request $request)
    {
        return $this->render('carte.html.twig');
    }

    /**
     * @Route("/traccia.gpx", name="traccia")
     **/
    public function traccia(Request $request)
    {
        return $this->render('traccia.html.twig');
    }
}
