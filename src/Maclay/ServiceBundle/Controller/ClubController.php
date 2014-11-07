<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Maclay\ServiceBundle\Entity\Record;
use Maclay\ServiceBundle\Form\ClubRecordType;
use Maclay\ServiceBundle\Model\ClubBatchRecord;
use Maclay\ServiceBundle\Model\StudentHours;
use Maclay\ServiceBundle\Form\ClubBatchRecordType;
use Maclay\ServiceBundle\Form\StudentHoursType;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClubController extends Controller
{
    private $schoolStart;
    private $q1End;
    private $q2End;
    private $q3End;
    private $q4End;
    
    public function manageClubAction($quarter = 0, $export = false){
        $user = $this->getUser();
        
        $clubs = $user->getSponsorForClubs();
        
        $startDate;
        $endDate;
        
        $this->schoolStart = new \DateTime($this->container->getParameter("schoolYearStart"));
        $this->q1End = new \DateTime($this->container->getParameter("q1End"));
        $this->q2End = new \DateTime($this->container->getParameter("q2End"));
        $this->q3End = new \DateTime($this->container->getParameter("q3End"));
        $this->q4End = new \DateTime($this->container->getParameter("q4End"));
        
        if ($quarter == 0){
            
        }
        else if($quarter == 1){
            $startDate = $this->schoolStart;
            $endDate = $this->q1End;
        }
        else if ($quarter == 2){
            $startDate = $this->q1End;
            $endDate = $this->q2End;
        }
        else if ($quarter == 3){
            $startDate = $this->q2End;
            $endDate = $this->q3End;
        }
        else if ($quarter == 4){
            $startDate = $this->q3End;
            $endDate = $this->q4End;
        }
        
        foreach ($clubs as $club){
            foreach($club->getMembers() as $member){
                $approvedHours = 0;
                $records = $member->getRecords();
                foreach($records as $record){
                    if ($record->getApprovalStatus() > 0){
                        if($quarter == 0){
                            $approvedHours += $record->getNumHours();
                        }
                        else if ($record->getDateTo() <= $endDate && $record->getDateTo() >= $startDate){
                            $approvedHours += $record->getNumHours();
                        }
                   }
                }
                $member->setApprovedHours($approvedHours);
            }
            
            if ($export == true){
                $response = new StreamedResponse();
                $response->setCallback(function() use ($club){
                    $handle = fopen('php://output', 'w+');
                    
                    fputcsv($handle, array('Last Name', 'First Name', 'Hours'),',');
                    
                    foreach($club->getMembers() as $member){
                        fputcsv($handle,array($member->getLastName(), $member->getFirstName(), $member->getApprovedHours()),',');
                    }
                    
                    fclose($handle);
                });
                
                $response->setStatusCode(200);
                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

                return $response;
            }
        }
        
        return $this->render("MaclayServiceBundle:Club:manage.html.twig", array("clubs" => $clubs, "error" => "", "quarter" => $quarter));
    }
    
    public function addClubMembersAction(){
        $user = $this->getUser();
        
        $club = $user->getSponsorForClubs()[0];
        
        return $this->render("MaclayServiceBundle:Club:addMembers.html.twig", array("error" => "", "clubId" => $club->getId()));
    }
    
    public function getUsersForClubAction($gender, $grade){
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("MaclayServiceBundle:User")->getUsersForClub($grade, $gender); 
        $userArray = array();
        foreach ($users as $user){
            $userArray[] = array("id" => $user->getId(), "lastName" => $user->getLastName(), "firstName" => $user->getFirstName());
        }
        $response = new Response(json_encode(array('users' => $userArray)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function addUserToClubAction($userId, $clubId){
        try{
            $em = $this->getDoctrine()->getManager();
        
            $user = $em->getRepository("MaclayServiceBundle:User")->findOneById($userId);
            $club = $em->getRepository("MaclayServiceBundle:Club")->findOneById($clubId);

            $user->addClub($club);
            $club->addMember($user);

            $em->flush();
            
            $response = new Response(json_encode(array("added" => true)));
            $response->headers->set("Content-Type", "application/json");
            return $response;
        }
        catch (\Exception $ee){
            $response = new Response(json_encode(array("added" => $ee->getMessage())));
            $response->headers->set("Content-Type", "application/json");
            return $response;
        }
    }
    
    public function newRecordForMemberAction(Request $request){
        $user = $this->getUser();
        
        $club = $user->getSponsorForClubs()[0];
        
        $students = $club->getMembers();
        
        $form = $this->createForm(new ClubRecordType($students), new Record());
        
        $form->handleRequest($request);
        
        if ($form->isValid()){
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


                $record->setCurrentGrade($record->getStudent()->getStudentInfo()->getGrade());
                $now = new \DateTime('now');
                $record->setDateCreated($now);
                $record->setApprovalStatus(0);
                $record->setEnteredByClub($club);

                $em = $this->getDoctrine()->getManager();
                $em->persist($record);
                $em->flush();
                return $this->render("MaclayServiceBundle:Club:newRecord.html.twig", array("error" => "Record Successfully Entered", "form" => $form->createView()));
            }
            catch (\Exception $ee){
                return $this->render("MaclayServiceBundle:Club:newRecord.html.twig", array("error" => $ee->getMessage(), "form" => $form->createView()));
            }
        }
        
        return $this->render("MaclayServiceBundle:Club:newRecord.html.twig", array("form" => $form->createView()));
    }
    
    public function batchEnterRecordAction(Request $request){
        $batchRecord = new ClubBatchRecord();
        $now = new \DateTime("now");
        $batchRecord->setDateFrom($now);
        $batchRecord->setDateTo($now);
        
        $club = $this->getUser()->getSponsorForClubs()[0];
        
        foreach($club->getMembers() as $member){
            $student = new StudentHours();
            $student->setLastName($member->getLastName());
            $student->setFirstName($member->getFirstName());
            $student->setStudentNumber($member->getStudentinfo()->getStudentNumber());
            $batchRecord->addStudentHours($student);
        }
        
        $form = $this->createForm(new ClubBatchRecordType(), $batchRecord);
        
        return $this->render("MaclayServiceBundle:Club:batchRecord.html.twig", array("form" => $form->createView()));
    }
}
