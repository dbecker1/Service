<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Maclay\ServiceBundle\Entity\Record;
use Maclay\ServiceBundle\Form\RecordType;
use Symfony\Component\HttpFoundation\Response;


class RecordController extends Controller
{
    public function recordSummaryAction()
    {
        $user = $this->getUser();
        $records = $user->getRecords();
        $approved = 0;
        $pending = 0;
        $denied = 0;
        foreach($records as $record){
            $status = $record->getApprovalStatus();
            if ($status > 0){
                 $approved += $record->getNumHours();
            }
            else if ($status < 0){
                $denied += $record->getNumHours();
            }
            else if($status == 0){
                $pending += $record->getNumHours();
            }
        }
        $hours = array('pending' => $pending, 'approved' => $approved, 'denied' => $denied);
        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository("MaclayServiceBundle:Record")
                ->getRecentRecords($this->container->getParameter("recentRecordLength") , $user->getId());
        $em->flush();
        return $this->render("MaclayServiceBundle:Record:recordSummary.html.twig", array("user" => $user, "records" => $records, "hours" => $hours));
    }
    
    public function newRecordAction()
    {
        $record = new Record();
        $form = $this->createForm(new RecordType(), $record, array(
            'action' => $this->generateUrl('default', array("controller" => "Record", "action" => "CreateRecord")),
        ));
        
        return $this->render("MaclayServiceBundle:Record:newRecord.html.twig", array("form" => $form->createView()));
    }
    
    public function createRecordAction(Request $request){
         $user = $this->getUser();
         
         $form = $this->createForm(new RecordType(), new Record());
         
         $form->handleRequest($request);
         
         if($form->isValid()){
             $record = $form->getData();
             $record->setCurrentGrade($user->getStudentInfo()->getGrade());
             $now = new \DateTime('now');
             $record->setDateCreated($now);
             $record->setStudent($user);
             $record->setApprovalStatus(0);
             
             $em = $this->getDoctrine()->getManager();
             $em->persist($record);
             $em->flush();
             return $this->redirect($this->generateUrl("default", array("controller" => "Record", "action" => "RecordSummary")));
         }
         
         return $this->render("MaclayServiceBundle:Record:newRecord.html.twig", array("form" => $form->createView()));
   }
   
   public function recordHistoryAction(){
       $user = $this->getUser();
       $records = $user->getRecords();
       return $this->render("MaclayServiceBundle:Record:recordHistory.html.twig", array("records" => $records));
   }
   
   public function getRecordPartialAction($id){
       $repository = $this->getDoctrine()->getRepository("MaclayServiceBundle:Record");
       $record = $repository->findOneById($id);
       $answer["html"] = $this->render("MaclayServiceBundle:Record:recordPartial.html.twig", array("record" => $record))->getContent();
       $response = new Response();                                         
       $response->headers->set('Content-type', 'application/json; charset=utf-8');
       $response->setContent(json_encode($answer));
       return $response;
   }
   
   public function pendingRecordsAction(){
       $repository = $this->getDoctrine()->getRepository("MaclayServiceBundle:Record");
       $records = $repository->getPendingRecords();
       
       return $this->render("MaclayServiceBundle:Record:pendingRecords.html.twig", array("records" => $records));
   }
   
   public function approveRecordAction($id, $approval){
       $em = $this->getDoctrine()->getManager();
       $repository = $em->getRepository("MaclayServiceBundle:Record");
       $record = $repository->findOneById($id);
       
       $answer["approved"] = false;
       $answer["denied"] = false;
       
       if ($approval === "true"){
           $record->setApprovalStatus(1);
           $answer["approved"] = true;
       }
       else if ($approval === "false"){
           $record->setApprovalStatus(-1);
           $answer["denied"] = true;
       }
       $em->flush();
       $response = new Response();                                         
       $response->headers->set('Content-type', 'application/json; charset=utf-8');
       $response->setContent(json_encode($answer));
       return $response;
   }
   
   public function studentHistoryAction(){
       $repository = $this->getDoctrine()->getRepository("MaclayServiceBundle:User");
       $students = $repository->findByRole("ROLE_STUDENT");
       foreach($students as $student){
           $numHours = 0;
           foreach($student->getRecords() as $record){
               if ($record->getApprovalStatus() > 0){
                   $numHours += $record->getNumHours();
               }
           }
           $student->setApprovedHours($numHours);
       }
               
       
       return $this->render("MaclayServiceBundle:Record:studentHistory.html.twig", array("students" => $students));
   }
   
   public function viewStudentAction($id){
       $repository = $this->getDoctrine()->getRepository("MaclayServiceBundle:User");
       $student = $repository->findOneById($id);
       $records = $student->getRecords();
       return $this->render("MaclayServiceBundle:Record:recordHistory.html.twig", array("records" => $records));
   }
}