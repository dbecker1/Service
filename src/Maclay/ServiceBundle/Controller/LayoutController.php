<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\Security\Core\Exception;

class LayoutController extends Controller
{
    public function titlebarAction()
    {
        try{
            $securityContext = $this->container->get('security.context');
            if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')){
            $isLoggedIn = true;
            }
            else{
                $isLoggedIn = false;
            }
        }
        catch (\Exception $e){
            $isLoggedIn = false;
        }
        
        return $this->render(
                "MaclayServiceBundle:Layout:titlebar.html.twig",
                array("isLoggedIn" => $isLoggedIn)
            );
    }
    
    public function navAction(){
        $securityContext = $this->container->get('security.context');
        $isCoordinator = $securityContext->isGranted("ROLE_COORDINATOR");
        $isStudent = $securityContext->isGranted("ROLE_STUDENT");
        $isAdmin = $securityContext->isGranted("ROLE_ADMIN");
        $isClubSponsor = $securityContext->isGranted("ROLE_CLUBSPONSOR");
        return $this->render("MaclayServiceBundle:Layout:nav.html.twig", array("isCoordinator" => $isCoordinator, "isStudent" => $isStudent, "isAdmin" => $isAdmin, "isClubSponsor" => $isClubSponsor));
    }
}
