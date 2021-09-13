<?php

namespace App\Controller\Robot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
