<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use AppBundle\Controller\CommonController;

use AppBundle\Entity\Achat;



use AppBundle\Form\AchatType;
use AppBundle\Form\DataType;


class AchatController extends CommonController
{
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
                $em->createQuery('DELETE FROM AppBundle:Achat')->execute();
                while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                    //if ($i == 0) { $i = 1;continue; }
                    $i += 1;
                    $rows = $data;
                    $em->getRepository('AppBundle:Achat')->addRows($rows);
                }
                return $this->redirectToRoute('achats');
            }
        }
        $em = $this->getDoctrine()->getManager();

        $achats = $em->getRepository('AppBundle:Achat')
        ->createQueryBuilder('p')
        ->where('p.campagne = :campagne')
        ->add('orderBy','p.date DESC, p.type ASC')
        ->setParameter('campagne', $campagne)
        ->getQuery()->getResult();

        return $this->render('AppBundle:Default:achats.html.twig', array(
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
            $achat = $em->getRepository('AppBundle:Achat')->findOneById($achat_id);
            $achat->name = $achat->produit->__toString();
            print($achat->name);
        }
        $form = $this->createForm(AchatType::class, $achat);
        $form->handleRequest($request);
        $produits = $em->getRepository('AppBundle:Produit')->getAllName($campagne);

        if ($form->isSubmitted()) {
            $em->getRepository('AppBundle:Achat')->save($achat, $campagne);
            return $this->redirectToRoute('achats');
        }
        return $this->render('AppBundle:Default:achat.html.twig', array(
            'form' => $form->createView(),
            'produits' => $produits,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }

    /**
     * @Route("/achat/{achat_id}/delete", name="achat_delete")
     **/
    public function achatDeleteAction($achat_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AppBundle:Achat')->delete($achat_id);
        return $this->redirectToRoute('achats');
    }

    /**
     * @Route("/achats_data", name="data")
     **/
    public function achatsDataEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(DataType::class);
        $form->handleRequest($request);

        $campagne = $this->getCurrentCampagne($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $em->getRepository('AppBundle:Achat')->saveCAJData($data['data'], $campagne);
            //return $this->redirectToRoute('achats');
        }
        return $this->render('AppBundle::base_form.html.twig', array(
            'form' => $form->createView(),
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }
}
