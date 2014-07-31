<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{
    public function profileAction()
    {
        $securityContext = $this->container->get("security.context");
        if($securityContext->isGranted("ROLE_STUDENT"))
        {
            return $this->forward("MaclayServiceBundle:Record:recordSummary");
        }
        return $this->render("MaclayServiceBundle:Profile:profile.html.twig");
    }
    
    
}
