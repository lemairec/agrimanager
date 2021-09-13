<?php

namespace App\Controller\Robot;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiRobotControlleurController extends AbstractController
{
    /**
     * @Route("/robot/api/get_order", name="robot_api")
     **/
    public function silo_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $temp = $request->query->get("robot_id");
        
        return new Response("WAIT");
    }

}
