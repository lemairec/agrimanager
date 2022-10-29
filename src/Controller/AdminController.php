<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Log;
use App\Entity\MetaCulture;

use App\Form\UserType;
use App\Form\CompanyAdminType;
use App\Form\MetaCultureType;

class AdminController extends CommonController
{

        /**
         * @Route("/admin/users", name="admin_users")
         */
        public function adminUsersAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository(User::class)->findAll();
            $logs = $em->getRepository(Log::class)->countByUser();
            $parcelles = $em->getRepository(Parcelle::class)->countByCompany();
            $interventions = $em->getRepository(Intervention::class)->countByCompany();

            $logs_dict = [];
            foreach ($logs as $log) {
                $logs_dict[$log['user_id']] = intval($log['count']);
            }

            $parcelles_dict = [];
            foreach ($parcelles as $parcelle) {
                $parcelles_dict[$parcelle['company_id']] = intval($parcelle['count']);
            }

            $interventions_dict = [];
            foreach ($interventions as $intervention) {
                $interventions_dict[$intervention['company_id']] = intval($intervention['count']);
            }

            foreach($users as $user){
                $companies = $em->getRepository(Company::class)->getAllForUser($user);
                $user->logs = 0;
                $user->parcelles = 0;
                $user->interventions = 0;
                if (isset($logs_dict[$user->id])){
                    $user->logs = $logs_dict[$user->id];
                }
                foreach ($companies as $company) {
                    if (isset($parcelles_dict[$company->id])){
                        $user->parcelles = $parcelles_dict[$company->id];
                    }
                    if (isset($interventions_dict[$company->id])){
                        $user->interventions = $interventions_dict[$company->id];
                    }
                }
            }

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

            $user = $em->getRepository(User::class)->findOneById($id);
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            $logs = $em->getRepository(Log::class)->findByUser($user);


            if ($form->isSubmitted()) {
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('admin_users');
            }
            return $this->render('Admin/user.html.twig', array(
                'form' => $form->createView(),
                'logs' => $logs,
                'user' => $user
            ));
        }

        /**
         * @Route("/admin/companies", name="admin_companies")
         */
        public function adminCompaniesAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $companies = $em->getRepository(Company::class)->findAll();

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
                $company = $em->getRepository(Company::class)->findOneById($id);
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

        /**
         * @Route("/admin/metacultures", name="admin_metacultures")
         */
        public function adminMetaCulturesAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $metacultures = $em->getRepository(MetaCulture::class)->findAll();

            return $this->render('Admin/metacultures.html.twig', array(
                'metacultures' => $metacultures
            ));

        }

        /**
         * @Route("/admin/metaculture/{id}", name="admin_metaculture")
         **/
        public function adminMetaCultureAction($id, Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            if($id == '0'){
                $metaculture = new MetaCulture();
            } else {
                $metaculture = $em->getRepository(MetaCulture::class)->findOneById($id);
            }
            $form = $this->createForm(MetaCultureType::class, $metaculture);
            $form->handleRequest($request);


            if ($form->isSubmitted()) {
                $em->persist($metaculture);
                $em->flush();
                return $this->redirectToRoute('admin_metacultures');
            }
            return $this->render('base_form.html.twig', array(
                'form' => $form->createView(),
            ));
        }
}
