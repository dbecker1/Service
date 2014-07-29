<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    public function loginAction()
    {
        $securityContext = $this->container->get("security.context");
        if ($securityContext->isGranted("IS_AUTHENTICATED_REMEMBERED")){
            return $this->redirect($this->generateUrl("maclay_service_profile"));
        }
        else {
            return $this->redirect($this->generateUrl("fos_user_security_login"));
        }
    }
    
    public function groupAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $group = new \Maclay\ServiceBundle\Entity\Role("Admin", array("ROLE_ADMIN"));
        $em->persist($group);
        $em->flush();
        return $this->redirect($this->generateUrl("maclay_service_login"));
    }
}
