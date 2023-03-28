<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use DateTime;

use App\Controller\CommonController;

use App\Entity\Group;
use App\Entity\Contact;
use App\Entity\User;

use App\Form\UserType;
use App\Form\CompanyAdminType;
use App\Form\ContactType;

class UserController extends CommonController
{
    #[Route(path: '/k8f96gtb', name: 'connection_user')]
    public function testAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user_id = $request->query->get('user_id');
        if($user_id == ''){
            return new Response("user not found");
        }
        $user = $em->getRepository(User::class)->findOneById($user_id);

        $token = new UsernamePasswordToken($user, $user->getPassword(), ["main"], $user->getRoles());
        $securityContext = $this->container->get('security.token_storage'); // do it your way
        $securityContext->setToken($token);
        //$this->get('session')->set('_security_main',serialize($token));
        $this->company = null;
        $this->campagne = null;

        $user->setLastLogin(new \DateTime());
        $em->persist($user);
        $em->flush();

        $session = $request->getSession();
        $session->clear();
        //print($this->getUser());

        //home
        return $this->redirectToRoute('home');
    }


    #[Route(path: '/profile/edit')]
    public function profileEditAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();

        $defaultData = array(
            'adresse' => $this->company->adresse,
            'codePostal' => $this->company->cityCode,
            'ville' => $this->company->city,
            'site1_name' => $this->company->site1_name,
            'site1_url' => $this->company->site1_url
        );
        $form = $this->createFormBuilder($defaultData)
            ->add('adresse', TextType::class)
            ->add('codePostal', TextType::class)
            ->add('ville', TextType::class)
            ->add('site1_name', TextType::class)
            ->add('site1_url', TextType::class)
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $data = $form->getData();
            $this->company->adresse = $data["adresse"];
            $this->company->cityCode = $data["codePostal"];
            $this->company->city = $data["ville"];
            $this->company->meteoCity = $data["ville"];
            $this->company->site1_name = $data["site1_name"];
            $this->company->site1_url = $data["site1_url"];
            $em->persist($this->company);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('Profile/profil.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route(path: '/contact/{contact_id}', name: 'contact')]
    public function contactAction($contact_id, Request $request,  MailerInterface $mailer)
    {
        $em = $this->getDoctrine()->getManager();

        if($contact_id == '0'){
            $contact = new Contact();
            $contact->datetime = new DateTime();
        } else {
            $contact = $em->getRepository(Contact::class)->findOneById($contact_id);
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($contact);
            $em->flush();
            $this->sendMail("noreply@maplaine.fr", "lemairec02@gmail.com", $contact->text, $mailer);
            return $this->redirectToRoute('contact_ok');
        }
        return $this->render('Profile/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route(path: '/contact_ok', name: 'contact_ok')]
    public function contactOkAction(Request $request)
    {
        return $this->render('Profile/contact_ok.html.twig');
    }
}
