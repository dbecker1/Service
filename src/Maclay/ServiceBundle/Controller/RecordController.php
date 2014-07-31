<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Maclay\ServiceBundle\Entity\Record;
use Maclay\ServiceBundle\Form\RecordType;

class RecordController extends Controller
{
    public function recordSummaryAction()
    {
        $user = $this->getUser();
        $records = $user->getRecords();
        return $this->render("MaclayServiceBundle:Record:recordSummary.html.twig", array("user" => $user));
    }
    
    public function newRecordAction()
    {
        $record = new Record();
        $form = $this->createForm(new RecordType(), $record, array(
            'action' => $this->generateUrl("record_create"),
        ));
        
        return $this->render("MaclayServiceBundle:Record:newRecord.html.twig", array("form" => $form->createView()));
    }
    
    public function createRecordAction(Request $request){
         $em = $this->getDoctrine()->getManager();
         
         $form = $this->createForm(new RecordType(), new Record());
         
         $form->handleRequest($request);
         
         if($form->isValid()){
             $record = $form->getData();
             return $this->render("MaclayServiceBundle:Record:recordSummary.html.twig");
         }
   }
}
