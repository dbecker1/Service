<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller is used for redirecting requests.
 */
class RoutingController extends Controller
{
    /**
     * The method for redirecting requests.
     * 
     * Although it is frowned upon by many other users of the Symfony Framework, this method takes a request in the form
     * or /Controller/Action and forwards it to the correct method. This is the way that ASP MVC does it, which is 
     * incredibly convient.
     * 
     * @param string $controller The controller to be redirected to.
     * @param string $action The action to be redirected to.
     */
    public function routeAction($controller, $action)
    {
        $route = "MaclayServiceBundle:" . $controller . ":" . lcfirst($action);

        return $this->forward($route);
    }
}
