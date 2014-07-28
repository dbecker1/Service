<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{
    public function profileAction()
    {
        $role = "";
        if ($this->get("security.context")->isGranted("ROLE_ADMIN")){
            $role = "Admin";
        }
        else{
            $role = "User";
        }
        return $this->render("MaclayServiceBundle:Profile:profile.html.twig", array("role" => $role));
    }
}
