<?php
namespace AgriBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CommonController extends Controller
{
    public function check_user($request){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        $this->getUser()->show_unity=true;
        $this->getUser()->show_unity=true;
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $this->company = $em->getRepository('AgriBundle:Company')->findOrCreate($user);

        $this->getUser()->show_unity=true;

        $show_unity = $request->query->get('show_unity');
        if($show_unity == 'false'){
            $this->getUser()->show_unity=false;
        }

        $this->saveLastUrl($request);
    }


    public function getCurrentCampagneId($request){
        $session = $request->getSession();

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
        $this->check_user($request);

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

    public function saveLastUrl($request){
        $last_url = $this->get('session')->get('last_url', []);
        $url = $request->getUri();
        if(count($last_url) > 0){
            if($last_url[count($last_url)-1] != $url){
                $last_url[] = $url;
                if(count($last_url) > 10){
                    array_shift($last_url);
                }
                $this->get('session')->set('last_url', $last_url);
            }
        }
    }

    public function popLastUrl($request){
        $last_url = $this->get('session')->get('last_url', []);
        array_pop($last_url);
        $this->get('session')->set('last_url', $last_url);
    }

    public function redirectPreviousPage($request){
        $last_url = $this->get('session')->get('last_url', []);
        $url = '/';
        if(count($last_url) > 1){
            $url = $last_url[count($last_url)-2];
        }
        return new RedirectResponse($url);
    }
}
