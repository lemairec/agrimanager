<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use App\Controller\CommonController;

use App\Entity\Achat;



use App\Form\AchatType;
use App\Form\DataType;


class AchatController extends CommonController
{
    /**
     * @Route("/achats", name = "achats")
     */
    public function achatsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $achats = $em->getRepository('App:Achat')
            ->createQueryBuilder('p')
            ->where('p.campagne = :campagne')
            ->add('orderBy','p.date DESC, p.type ASC')
            ->setParameter('campagne', $campagne)
            ->getQuery()->getResult();

        return $this->render('Default/achats.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'achats' => $achats,
            'navs' => ["Produits" => "produits", "Achats" => "achats"]
        ));
    }

    /**
     * @Route("/achat/{achat_id}", name="achat")
     **/
    public function achatEditAction($achat_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
        $produits = $em->getRepository('App:Produit')->findByCompany($campagne->company);
        if($achat_id == '0'){
            $achat = new Achat();
            $achat->date = new \DateTime();
        } else {
            $achat = $em->getRepository('App:Achat')->findOneById($achat_id);
            $achat->name = $achat->produit->__toString();
        }
        $form = $this->createForm(AchatType::class, $achat, array(
            'produits' => $produits
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->getRepository('App:Achat')->save($achat, $campagne);
            return $this->redirectToRoute('achats');
        }
        return $this->render('Default/achat.html.twig', array(
            'form' => $form->createView(),
            'produits' => $produits,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'navs' => ["Produits" => "produits", "Achats" => "achats"]
        ));
    }

    /**
     * @Route("/achat/{achat_id}/delete", name="achat_delete")
     **/
    public function achatDeleteAction($achat_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:Achat')->delete($achat_id);
        return $this->redirectToRoute('achats');
    }

    /**
     * @Route("/achats_data", name="achats_data")
     **/
    public function achatsDataEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(DataType::class);
        $form->handleRequest($request);

        $campagne = $this->getCurrentCampagne($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $em->getRepository('App:Achat')->saveCAJData($data['data'], $campagne);
            //return $this->redirectToRoute('achats');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }
}
