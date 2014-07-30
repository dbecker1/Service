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
            return $this->forward("MaclayServiceBundle:Profile:recordSummary");
        }
        return $this->render("MaclayServiceBundle:Profile:profile.html.twig");
    }
    
    public function recordSummaryAction()
    {
        $user = $this->getUser();
        $records = $user->getRecords();
        return $this->render("MaclayServiceBundle:Profile:recordSummary.html.twig", array("user" => $user));
    }
}
