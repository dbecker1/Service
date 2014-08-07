<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RoutingController extends Controller
{
    public function routeAction($controller, $action)
    {
        $route = "MaclayServiceBundle:" . $controller . ":" . strtolower($action);

        return $this->forward($route);
    }
}
