<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Group;

use AppBundle\Form\UserType;
use AppBundle\Form\CompanyAdminType;

class DefaultController extends Controller
{
    /**
     * @Route("/init", name="homepage")
     */
    public function initAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('AppBundle:Group')->findOneByName('admin');
        if($group == NULL){
            $group = new Group("admin",['ROLE_ADMIN']);
            $em->persist($group);
            $em->flush();
        }
        $users = $em->getRepository('AppBundle:User')->findUsersContainGroup($group);
        if(count($users) == 0){
            $users = $em->getRepository('AppBundle:User')->findAll($group);
            foreach($users as $user){
                $user->groups = [$group];
            }
            $em->persist($user);
            $em->flush();
            print("toto");
        }
        // replace this example code with whatever you need
        return new Response("ok");
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function adminUsersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('AppBundle:Admin:users.html.twig', array(
            'users' => $users
        ));

    }

    /**
     * @Route("admin/user/{id}", name="admin_user")
     **/
    public function adminUserAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($id == '0'){
            return;
        } else {
            $user = $em->getRepository('AppBundle:User')->findOneById($id);
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/companies", name="admin_companies")
     */
    public function adminCompaniesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $companies = $em->getRepository('AgriBundle:Company')->findAll();

        return $this->render('AppBundle:Admin:companies.html.twig', array(
            'companies' => $companies
        ));

    }

    /**
     * @Route("/admin/company/{id}", name="admin_company")
     **/
    public function adminCompanyeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($id == '0'){
            return;
        } else {
            $company = $em->getRepository('AgriBundle:Company')->findOneById($id);
        }
        $form = $this->createForm(CompanyAdminType::class, $company);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($company);
            $em->flush();
            return $this->redirectToRoute('admin_companies');
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
