<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use DateTime;

use App\Controller\CommonController;

use App\Entity\Group;
use App\Entity\Contact;

use App\Form\UserType;
use App\Form\CompanyAdminType;
use App\Form\ContactType;

class UserController extends CommonController
{
    /**
     * @Route("/k8f96gtb", name="connection_user")
     */
    public function testAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user_id = $request->query->get('user_id');
        if($user_id == ''){
            return new Response("user not found");
        }
        $user = $em->getRepository('App:User')->findOneById($user_id);

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
        return $this->render('Default/profil.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/contact/{contact_id}", name="contact")
     **/
    public function contactAction($contact_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($contact_id == '0'){
            $contact = new Contact();
            $contact->datetime = new DateTime();
        } else {
            $contact = $em->getRepository('App:Contact')->findOneById($contact_id);
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($contact);
            $em->flush();
            $this->sendMail("noreply@maplaine.fr", "lemairec02@gmail.com", "Contact", $contact->text);
            return $this->redirectToRoute('contact_ok');
        }
        return $this->render('Profile/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/contact_ok", name="contact_ok")
     **/
    public function contactOkAction(Request $request)
    {
        return $this->render('Profile/contact_ok.html.twig');
    }
}
