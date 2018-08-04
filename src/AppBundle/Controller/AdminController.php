<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Group;

use AppBundle\Form\UserType;
use AppBundle\Form\CompanyAdminType;

class AdminController extends Controller
{

        /**
         * @Route("/admin/users", name="admin_users")
         */
        public function adminUsersAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository('AppBundle:User')->findAll();

            return $this->render('Admin/users.html.twig', array(
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
            return $this->render('base_form.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        /**
         * @Route("/admin/companies", name="admin_companies")
         */
        public function adminCompaniesAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $companies = $em->getRepository('AppBundle:Company')->findAll();

            return $this->render('Admin/companies.html.twig', array(
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
                $company = $em->getRepository('AppBundle:Company')->findOneById($id);
            }
            $form = $this->createForm(CompanyAdminType::class, $company);
            $form->handleRequest($request);


            if ($form->isSubmitted()) {
                $em->persist($company);
                $em->flush();
                return $this->redirectToRoute('admin_companies');
            }
            return $this->render('base_form.html.twig', array(
                'form' => $form->createView(),
            ));
        }
}
