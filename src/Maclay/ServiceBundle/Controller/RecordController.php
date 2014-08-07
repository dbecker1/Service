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
         $user = $this->get("security.context")->getToken()->getUser();
         
         $form = $this->createForm(new RecordType(), new Record());
         
         $form->handleRequest($request);
         
         if($form->isValid()){
             $record = $form->getData();
             $record->setCurrentGrade($user->getStudentInfo()->getGrade());
             $now = new \DateTime('now');
             $record->setDateCreated($now);
             $record->setStudent($user);
             
             $em = $this->getDoctrine()->getManager();
             $em->persist($record);
             $em->flush();
             return $this->redirect($this->generateUrl("default", array("controller" => "Record", "action" => "RecordSummary")));
         }
   }
}
