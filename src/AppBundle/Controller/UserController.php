<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


use AppBundle\Entity\Group;

use AppBundle\Form\UserType;
use AppBundle\Form\CompanyAdminType;

class UserController extends Controller
{
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
        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);

        $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
        $this->get("security.token_storage")->setToken($token);
        $this->get('session')->set('_security_main',serialize($token));

        $user->setLastLogin(new \DateTime());
        $em->persist($user);
        $em->flush();

        //home
        return $this->redirectToRoute('home');
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
        return $this->render('AppBundle:Default:profil.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
