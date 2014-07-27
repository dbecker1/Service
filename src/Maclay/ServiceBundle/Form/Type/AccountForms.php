<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function loginAction()
    {
        return $this->render("MaclayServiceBundle:Account:login.html.twig");
    }
}
