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
    
    public function newRecordAction(Request $request){
         $user = $this->getUser();
         
         $now = new \DateTime('now');
         $record = new Record();
         $record->setDateFrom($now);
         $record->setDateTo($now);
         
         $form = $this->createForm(new RecordType(), $record);
         
         $form->handleRequest($request);
         
         if($form->isValid()){
             try
             {
                $path = $this->container->getParameter("recordUploadDirectory");
                $file = $form["attachment"]->getData();
                $record = $form->getData();
                if ($file !== NULL){
                    $extension = $file->guessExtension();
                    if (!$extension){
                        $extension = "bin";
                    }
                    $fileName = $user->getUsername() . rand(1,999999) . "." . $extension;
                    $file->move($path, $fileName);
                    
                    $record->setAttachmentFileName($fileName);
                }
                
                
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
             catch (\Exception $ee)
             {
                return $this->render("MaclayServiceBundle:Record:newRecord.html.twig", array("error" => $ee->getMessage(), "form" => $form->createView()));
             }
         }
         
         return $this->render("MaclayServiceBundle:Record:newRecord.html.twig", array("error" => "", "form" => $form->createView()));
   }
   
   public function recordHistoryAction(){
       $user = $this->getUser();
       $records = $user->getRecords();
       return $this->render("MaclayServiceBundle:Record:recordHistory.html.twig", array("records" => $records));
   }
   
   public function getRecordPartialAction($id, $isCoordinator = false){
       $repository = $this->getDoctrine()->getRepository("MaclayServiceBundle:Record");
       $record = $repository->findOneById($id);
       $path = "";
       if ($record->getAttachmentFileName() !== NULL){
           $path = $this->container->getParameter("recordFileViewLink") . $record->getAttachmentFileName();
       }
       $answer["html"] = $this->render("MaclayServiceBundle:Record:recordPartial.html.twig", array("record" => $record, "path" => $path, "isCoordinator" => $isCoordinator))->getContent();
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
       
       if ($approval === "true"){
           try{
                $student = $record->getStudent();

                if (in_array(@$_SERVER['REMOTE_ADDR'], array(
                     '127.0.0.1',
                     '::1',
                 ))) {
                     $transport = \Swift_SmtpTransport::newInstance('smtp.office365.com', 587, "tls")
                     ->setUsername('maclayservice@maclay.org')
                     ->setPassword('GoMarauders2014')
                     ;
                 }
                 else{
                     $transport = \Swift_SmtpTransport::newInstance('localhost');
                 }

                 $mailer = \Swift_Mailer::newInstance($transport);

                 $body = $this->render("MaclayServiceBundle:Email:recordApproved.html.twig", array("name" => $student->getFirstName(), "record" => $record))->getContent();

                 $message = \Swift_Message::newInstance('Record Approved')
                     ->setFrom(array("maclayservice@maclay.org" => "Maclay School Community Service"))
                     ->setReplyTo(array("maclayservice@maclay.org" => "Maclay School Community Service"))
                     ->setTo($student->getEmail())
                     ->setBody($body, "text/html")
                     ;

                 $mailer->send($message);
            } catch (\Exception $ex) {
                throw $ex;
            }
       }
       
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
       $name = $student->getFirstName() . " " . $student->getLastName();
       $isCoordinator = $this->getUser()->hasRole("ROLE_COORDINATOR");
       return $this->render("MaclayServiceBundle:Record:recordHistory.html.twig", array("records" => $records, "isCoordinator" => $isCoordinator, "name" => $name));
   }
   
   public function printRecordAction($id){
       $repository = $this->getDoctrine()->getRepository("MaclayServiceBundle:Record");
       $record = $repository->getRecordAndStudentById($id)[0];
       $club = $record->getEnteredByClub();
       if ($club === NULL){
           return $this->render("MaclayServiceBundle:Record:printRecord.html.twig", array("record" => $record));
       }
       else {
           return $this->render("MaclayServiceBundle:Record:printRecord.html.twig", array("record" => $record, "clubName" => $club->getClubName()));
       }
       
   }
   
   public function recordEmailAction($id){
       $repository = $this->getDoctrine()->getRepository("MaclayServiceBundle:Record");
       $record = $repository->findOneById($id); 
       $student = $record->getStudent();
       
       $response = new Response();                                         
       $response->headers->set('Content-type', 'application/json; charset=utf-8');
       
       try{
            if (in_array(@$_SERVER['REMOTE_ADDR'], array(
                '127.0.0.1',
                '::1',
            ))) {
                $transport = \Swift_SmtpTransport::newInstance('smtp.office365.com', 587, "tls")
                ->setUsername('maclayservice@maclay.org')
                ->setPassword('GoMarauders2014')
                ;
            }
            else{
                $transport = \Swift_SmtpTransport::newInstance('localhost');
            }

            $mailer = \Swift_Mailer::newInstance($transport);

            $body = $this->render("MaclayServiceBundle:Email:recordEmail.html.twig", array("name" => $student->getFirstName(), "record" => $record))->getContent();

            $message = \Swift_Message::newInstance('Missing Attachment.')
                ->setFrom(array("maclayservice@maclay.org" => "Maclay School Community Service"))
                ->setReplyTo(array("maclayservice@maclay.org" => "Maclay School Community Service"))
                ->setTo($student->getEmail())
                ->setBody($body, "text/html")
                ;

            $mailer->send($message);
            
            $answer["error"] = "sent";
            
            $record->setEmailIsSent(true);
            $this->getDoctrine()->getManager()->flush();
       } catch (\Exception $ee){
           $answer["error"] = $ee->getMessage();
       }
       
       $response->setContent(json_encode($answer));
       return $response;
   }
}
