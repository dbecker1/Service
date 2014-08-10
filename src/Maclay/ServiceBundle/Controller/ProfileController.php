<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{
    public function profileAction()
    {
        $securityContext = $this->container->get("security.context");
        if($securityContext->isGranted("ROLE_COORDINATOR"))
        {
            return $this->redirect($this->generateUrl('default', array("controller" => "Record", "action" => "PendingRecords")));
        }
        else if($securityContext->isGranted("ROLE_STUDENT"))
        {
            return $this->redirect($this->generateUrl('default', array("controller" => "Record", "action" => "RecordSummary")));
        }
        return $this->render("MaclayServiceBundle:Profile:profile.html.twig");
    }
    
    
}
