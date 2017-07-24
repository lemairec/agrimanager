<?php

namespace AgriBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Datetime;


use AgriBundle\Controller\CommonController;
use AgriBundle\Entity\Achat;
use AgriBundle\Entity\Ilot;
use AgriBundle\Entity\Campagne;
use AgriBundle\Entity\Intervention;
use AgriBundle\Entity\InterventionParcelle;
use AgriBundle\Entity\InterventionMateriel;
use AgriBundle\Entity\MaterielEntretien;
use AgriBundle\Entity\InterventionProduit;
use AgriBundle\Repository\IlotRepository;
use AgriBundle\Entity\Parcelle;
use AgriBundle\Entity\Materiel;
use AgriBundle\Entity\Livraison;
use AgriBundle\Entity\Produit;
use AgriBundle\Entity\Culture;

use AgriBundle\Form\InterventionType;
use AgriBundle\Form\CampagneType;
use AgriBundle\Form\CompanyType;
use AgriBundle\Form\CultureType;
use AgriBundle\Form\LivraisonType;
use AgriBundle\Form\ParcelleType;
use AgriBundle\Form\MaterielType;
use AgriBundle\Form\ProduitType;
use AgriBundle\Form\IlotType;
use AgriBundle\Form\AchatType;
use AgriBundle\Form\InterventionParcelleType;
use AgriBundle\Form\InterventionMaterielType;
use AgriBundle\Form\MaterielEntretienType;
use AgriBundle\Form\InterventionProduitType;

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
     * @Route("/k8f96gtb")
     */
    public function testAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user_id = $request->query->get('user_id');
        if($user_id == ''){
            return new Response("user not found");
        }
        $user = $em->getRepository('AgriBundle:User')->findOneById($user_id);

        $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
        $this->get("security.token_storage")->setToken($token);
        $this->get('session')->set('_security_main',serialize($token));

        //return $this->render('AgriBundle::base_agri.html.twig');
        return $this->redirectToRoute("home");
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
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        //Si le visiteur est déjà identifié, on le redirige vers l'accueil
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('home');
        }

        // Le service authentication_utils permet de récupérer le nom d'utilisateur
        // et l'erreur dans le cas où le formulaire a déjà été soumis mais était invalide
        // (mauvais mot de passe par exemple)
        $authenticationUtils = $this->get('security.authentication_utils');
        return $this->render('AgriBundle:Default:login.html.twig', array(
        'last_username' => $authenticationUtils->getLastUsername(),
        'error'         => $authenticationUtils->getLastAuthenticationError(),
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
            if($p->active){
                if (!array_key_exists($p->culture, $cultures)) {
                    $cultures[$p->culture] = 0;
                }
                $cultures[$p->culture] += $p->surface;
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
        if($parcelle_id == '0'){
            $parcelle = new Parcelle();
            $parcelle->campagne = $campagne;
        } else {
            $parcelle = $em->getRepository('AgriBundle:Parcelle')->findOneById($parcelle_id);
        }
        $form = $this->createForm(ParcelleType::class, $parcelle, array(
            'ilots' => $ilots
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
                    $parcelles[$livraison->parcelle->id] = ['name'=>$livraison->parcelle->completeName, 'espece' => $livraison->espece, 'surface'=>$livraison->parcelle->surface, 'poid' => 0];
                }
                $parcelles[$livraison->parcelle->id]['poid'] += $livraison->poid_norme;
            }
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
     * @Route("/produits", name="produits")
     */
    public function produitsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('AgriBundle:Produit')
            ->createQueryBuilder('p')
            ->where('p.campagne = :campagne')
            ->add('orderBy','p.type ASC, p.name ASC')
            ->setParameter('campagne', $campagne)
            ->getQuery()->getResult();

        return $this->render('AgriBundle:Default:stocks.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'stocks' => $produits,
        ));
    }

    /**
     * @Route("/produit/{produit_id}", name="produit")
     **/
    public function produitEditAction($produit_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        $interventions = [];
        $achats = [];
        if($produit_id == '0'){
            $produit = new Produit();
        } else {
            $produit = $em->getRepository('AgriBundle:Produit')->findOneById($produit_id);
            $interventions = $em->getRepository('AgriBundle:Intervention')->getAllForProduit($produit);
            $achats = $em->getRepository('AgriBundle:Achat')->getAllForProduit($produit);
        }
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $produit->campagne = $campagne;
            $em->getRepository('AgriBundle:Produit')->update($produit);
            return $this->redirectToRoute('produits');
        }
        return $this->render('AgriBundle:Default:produit.html.twig', array(
            'form' => $form->createView(),
            'interventions' => $interventions,
            'achats' => $achats,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }

    /**
     * @Route("/produit/{produit_id}/delete", name="produit_delete")
     **/
    public function produitDeleteAction($produit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AgriBundle:Produit')->delete($produit_id);
        return $this->redirectToRoute('produits');
    }

    /**
     * @Route("/stocks")
     */
    public function stocksAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('AgriBundle:Produit')
            ->createQueryBuilder('p')
            ->where('p.campagne = :campagne')
            ->andWhere('ABS(p.qty) > 0.001')
            ->add('orderBy','p.type ASC, p.name ASC')
            ->setParameter('campagne', $campagne)
            ->getQuery()->getResult();

        return $this->render('AgriBundle:Default:stocks.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'stocks' => $produits,
        ));
    }

    /**
     * @Route("/achats", name = "achats")
     */
    public function achatsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('file');
            $dir = $this->get('kernel')->getRootDir() . '/../web/uploads/images/';
            $fileName = $file->move($dir, "temp.csv");
            if (($handle = fopen($fileName, "r")) !== FALSE) {
                $i = 0;
                $em->createQuery('DELETE FROM AgriBundle:Achat')->execute();
                while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                    //if ($i == 0) { $i = 1;continue; }
                    $i += 1;
                    $rows = $data;
                    $em->getRepository('AgriBundle:Achat')->addRows($rows);
                }
                return $this->redirectToRoute('achats');
            }
        }
        $em = $this->getDoctrine()->getManager();

        $achats = $em->getRepository('AgriBundle:Achat')
        ->createQueryBuilder('p')
        ->where('p.campagne = :campagne')
        ->add('orderBy','p.date DESC, p.type ASC')
        ->setParameter('campagne', $campagne)
        ->getQuery()->getResult();

        return $this->render('AgriBundle:Default:achats.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'achats' => $achats,
        ));
    }

    /**
     * @Route("/achat/{achat_id}", name="achat")
     **/
    public function achatEditAction($achat_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        if($achat_id == '0'){
            $achat = new Achat();
            $achat->date = new \DateTime();
        } else {
            $achat = $em->getRepository('AgriBundle:Achat')->findOneById($achat_id);
        }
        $form = $this->createForm(AchatType::class, $achat);
        $form->handleRequest($request);
        $produits = $em->getRepository('AgriBundle:Produit')->getAllName($campagne);

        if ($form->isSubmitted()) {
            $em->getRepository('AgriBundle:Achat')->save($achat, $campagne);
            return $this->redirectToRoute('achats');
        }
        return $this->render('AgriBundle:Default:achat.html.twig', array(
            'form' => $form->createView(),
            'produits' => $produits,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }

    /**
     * @Route("/bilan_detail", name="bilan_detail")
     */
    public function bilanDetailAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];

        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagne($campagne);
        $parcelles = $em->getRepository('AgriBundle:Parcelle')
        ->createQueryBuilder('p')
        ->where('p.campagne = :campagne')
        ->add('orderBy','p.culture DESC, p.ilot ASC')
        ->setParameters(array('campagne'=>$campagne))
        ->getQuery()->getResult();
        foreach ($parcelles as $p) {
            if (!array_key_exists($p->culture, $cultures)) {
                $cultures[$p->culture] = 0;
            }
            $cultures[$p->culture] += $p->surface;

            $p->interventions = [];
            if($p->id != '0'){
                $p->interventions = $em->getRepository('AgriBundle:Intervention')->getAllForParcelle($p);
            }
            $p->n = 0;
            $p->p = 0;
            $p->k = 0;
            $p->mg = 0;
            $p->s = 0;
            $p->priceHa = 0;
            foreach($p->interventions as $it){
                $p->priceHa += $it->getPriceHa();
                foreach($it->produits as $produit){
                    $p->n += $produit->getQtyHa() * $produit->produit->n;
                    $p->p += $produit->getQtyHa() * $produit->produit->p;
                    $p->k += $produit->getQtyHa() * $produit->produit->k;
                    $p->mg += $produit->getQtyHa() * $produit->produit->mg;
                    $p->s += $produit->getQtyHa() * $produit->produit->s;
                }
            }
        }
        return $this->render('AgriBundle:Default:bilan_detail.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/bilan", name="bilan")
     */
    public function bilanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];

        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagneWithoutActive($campagne);

        foreach ($parcelles as $p) {
            if (!array_key_exists($p->culture, $cultures)) {
                $cultures[$p->culture] = ['culture'=>$p->culture,'surface'=>0, 'priceHa'=>0, 'rendement'=>0];
            }


            $p->interventions = [];
            if($p->id != '0'){
                $p->interventions = $em->getRepository('AgriBundle:Intervention')->getAllForParcelle($p);
            }
            $p->n = 0;
            $p->p = 0;
            $p->k = 0;
            $p->mg = 0;
            $p->s = 0;
            $p->priceHa = 0;
            foreach($p->interventions as $it){
                $p->priceHa += $it->getPriceHa();
                foreach($it->produits as $produit){
                    $p->n += $produit->getQtyHa() * $produit->produit->n;
                    $p->p += $produit->getQtyHa() * $produit->produit->p;
                    $p->k += $produit->getQtyHa() * $produit->produit->k;
                    $p->mg += $produit->getQtyHa() * $produit->produit->mg;
                    $p->s += $produit->getQtyHa() * $produit->produit->s;
                }
            }

            $cultures[$p->culture]['surface'] += $p->surface;
            $cultures[$p->culture]['priceHa'] += $p->surface*$p->priceHa;
            $cultures[$p->culture]['rendement'] += $p->surface*$p->rendement;

        }

        return $this->render('AgriBundle:Default:bilan.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/calendar", name="calendar")
     **/
    public function calendar(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $interventions = $em->getRepository('AgriBundle:Intervention')->getAllForCompany($this->company);
        return $this->render('AgriBundle:Default:calendar.html.twig', array(
            'interventions' => $interventions
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
}
