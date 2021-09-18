<?php

namespace App\Controller\Robot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Robot\Order;
use DateTime;

class RobotControlleurController extends CommonController
{
    /**
     * @Route("/robots", name="robots")
     **/
    public function robots(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robots = $em->getRepository("App:Robot\Robot")->findAll();
        return $this->render('robot/robots.html.twig', array(
            'robots' => $robots,
        ));
    }

    /**
     * @Route("/robot/{robot_id}", name="robot")
     **/
    public function robot($robot_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $robot = $em->getRepository("App:Robot\Robot")->findOneByName($robot_id);
        $orders = $em->getRepository("App:Robot\Order")->getLast10ForRobot($robot);
        $data = json_encode($robot->last_data);
        return $this->render('robot/robot.html.twig', array(
            'robot_id' => $robot_id,
            'orders' => $orders,
            'robot' => $robot,
            'robot_data' => $data
        ));
    }

    /**
     * @Route("/robot_order/{robot_id}/{order_label}", name="robot_order")
     **/
    public function robot_order($robot_id, $order_label, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robot = $em->getRepository("App:Robot\Robot")->findOneByName($robot_id);
        print("otot");
        print($robot->id);
        
        $order = new Order();
        $order->robot = $robot;
        $order->name = $order_label;
        $order->d_create = new \DateTime();

        $em->persist($order);
        $em->flush();
        return $this->redirectToRoute('robot', array('robot_id' => $robot_id));
    }

}
