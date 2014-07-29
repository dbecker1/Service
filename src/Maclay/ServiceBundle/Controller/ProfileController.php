<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{
    public function profileAction()
    {
        
        return $this->render("MaclayServiceBundle:Profile:profile.html.twig");
    }
}
