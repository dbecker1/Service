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
}
