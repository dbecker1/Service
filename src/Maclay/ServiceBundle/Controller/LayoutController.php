<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\Security\Core\Exception;

/**
 * This controller contains methods for creating the dynamic aspects of the layout
 */
class LayoutController extends Controller
{
    /**
     * This method creates the title bar of the layout, which contains the users name and a log out button
     */
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
    
    /**
     * This method generates the nav bar on the left of the layout that changes depending on the roles of the user.
     */
    public function navAction(){
        $securityContext = $this->container->get('security.context');
        $isCoordinator = $securityContext->isGranted("ROLE_COORDINATOR");
        $isStudent = $securityContext->isGranted("ROLE_STUDENT");
        $isAdmin = $securityContext->isGranted("ROLE_ADMIN");
        $isClubSponsor = $securityContext->isGranted("ROLE_CLUBSPONSOR");
        $isSchoolAdmin = $securityContext->isGranted("ROLE_SCHOOLADMIN");
        $downloadLink = $this->container->getParameter("recordFormDownloadLink");
        return $this->render("MaclayServiceBundle:Layout:nav.html.twig", array("isSchoolAdmin" => $isSchoolAdmin, "isCoordinator" => $isCoordinator, "isStudent" => $isStudent, "isAdmin" => $isAdmin, "isClubSponsor" => $isClubSponsor, "downloadLink" => $downloadLink));
    }
}
