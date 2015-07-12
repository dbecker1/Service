<?php

namespace Maclay\ServiceBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MaintenanceListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $maintenance = $this->container->hasParameter('maintenance') ? $this->container->getParameter('maintenance') : false;
        $debug = in_array($this->container->get('kernel')->getEnvironment(), array('test', 'dev'));
            
        if ($maintenance && !$debug) {
            $engine = $this->container->get('templating');
            $content;
            try{
                $content = $engine->render('::maintenance.html.twig');
            } catch (\Exception $e){
                $content = $e->getMessage();
            }
            $response = new Response();
            $response->setContent($content);
            $response->setStatusCode(503);
            $event->setResponse($response);
            $event->stopPropagation();
        }

    }
}