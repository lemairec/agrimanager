<?php
namespace AgriBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommonController extends Controller
{
    public function check_user(){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $this->company = $em->getRepository('AgriBundle:Company')->findOrCreate($user);


    }


    public function getCurrentCampagneId($request){
        $session = $request->getSession();

        $show_unity = $request->query->get('show_unity');
        if($show_unity != 'false'){
            $this->getUser()->show_unity=true;
        } else {
            $this->getUser()->show_unity=false;
        }
        
        $campagne_id = $session->get('campagne_id', '');
        if($campagne_id == ''){
            $em = $this->getDoctrine()->getManager();
            $campagne = $em->getRepository('AgriBundle:Campagne')->findAll()[0]->id;
        }
        $new_campagne_id = $request->query->get('campagne_id');
        if($new_campagne_id != ''){
            $session->set('campagne_id', $new_campagne_id);
            return $new_campagne_id;
        }



        return $campagne_id;
    }

    public function getCurrentCampagne($request){
        $campagne_id = $this->getCurrentCampagneId($request);
        $em = $this->getDoctrine()->getManager();
        $this->check_user();

        $campagne = $em->getRepository('AgriBundle:Campagne')->findOneById($campagne_id);
        if(!property_exists($this, 'campagnes')){
            $this->campagnes = $em->getRepository('AgriBundle:Campagne')->getAllforCompany($this->company);
        }

        if($campagne){
            return $campagne;
        } else {
            return $this->campagnes[0];
        }
    }
}
