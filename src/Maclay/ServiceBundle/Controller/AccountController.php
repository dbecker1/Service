<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    public function loginAction()
    {
        return $this->render("MaclayServiceBundle:Account:login.html.twig");
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
