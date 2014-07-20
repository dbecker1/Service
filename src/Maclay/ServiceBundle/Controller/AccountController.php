<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function loginAction()
    {
        return new Response("<html><body><h1>THIS CRAP FINALLY WORKS</h1></body></html>");
    }
}
