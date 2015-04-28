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

/**
 * The controller for club methods.
 * 
 * This controller contians methods that the club sponsors will use. 
 */
class ClubController extends Controller
{
    /**
     * @var DateTime $schoolStart The start of the school year.
     */
    private $schoolStart;
    
    /**
     * @var DateTime $q1End The end of the 1st quarter.
     */
    private $q1End;
    
    /**
     * @var DateTime $q1End The end of the 2nd quarter.
     */
    private $q2End;
    
    /**
     * @var DateTime $q1End The end of the 3rd quarter.
     */
    private $q3End;
    
    /**
     * @var DateTime $q1End The end of the 4th quarter.
     */
    private $q4End;
    
    /**
     * The method for the club sponsor's home page.
     * 
     * This is the main page of the club sponsor. It shows each memeber and their total number of approved.
     * There is then a dropdown to narrow down the hours by quarter and then an export function that will export 
     * them to a CSV file that is readable by Excel.
     * 
     * @param int $quarter Optional. Used to narrow down approved totals.
     * @param boolean $export Optional. Used to export approved totals.
     */
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
                            if(strpos($record->getActivity(), "Grade Hours") !== true){
                                $approvedHours += $record->getNumHours();
                            }
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
    
    /*
     * This method simply returns the view for adding members to a club.
     */
    public function addClubMembersAction(){
        $user = $this->getUser();
        
        $club = $user->getSponsorForClubs()[0];
        
        return $this->render("MaclayServiceBundle:Club:addMembers.html.twig", array("error" => "", "clubId" => $club->getId()));
    }
    
    /*
     * The method for getting users to be added to a club.
     * 
     * This method returns students by gender and grade. This is called by Ajax from the addClubMember page. The goal
     * of this method is to narrow down the list of users for club sponsors to add members to their club with.
     * 
     * @param string $gender The desired gender of the users.
     * @param int $grade The desired grade of the users.
     * @return array $users The users with the matching gender and grade.
     */
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
    
    /**
     * The method for adding users to a club.
     * 
     * This method is called by Ajax from the addClubMembers page. It takes a user id and adds that user to a club
     * with the club id.
     * 
     * @param int $userId The ID of the user to be added to the club.
     * @param int $clubId The ID of the club that the user should be added to.
     */
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
    
    /**
     * The method for a sponsor to add a record for a member.
     * 
     * This method can be used by club sponsors to add a record for a single member.
     * 
     * @param Request $request The form containing the record to be added.
     */
    public function newRecordForMemberAction(Request $request){
        $user = $this->getUser();
        
        $club = $user->getSponsorForClubs()[0];
        
        $students = $club->getMembers();
        
        $now = new \DateTime('now');
        $record = new Record();
        $record->setDateFrom($now);
        $record->setDateTo($now);
        
        $form = $this->createForm(new ClubRecordType($students), $record);
        
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
    
    /**
     * The method for entering records for members in batch.
     * 
     * This method is for when club sponsors want to enter multiple records with the same details for multiple users.
     * The sponsors have the freedom to put in a number of hours for each individual student.
     * 
     * @param Reqest $request The form containing the records to be added.
     */
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
            $student->setNumHours(0);
            $batchRecord->addStudentHours($student);
        }
        
        $form = $this->createForm(new ClubBatchRecordType(), $batchRecord);
        
        $form->handleRequest($request);
        
        if ($form->isValid()){
            try{
                $batchRecord = $form->getData();

                $repository = $this->getDoctrine()->getRepository("MaclayServiceBundle:User");
                $em = $this->getDoctrine()->getManager();

                foreach ($batchRecord->getStudentHours() as $studentHour){
                    if ($studentHour->getNumHours() == 0)
                        continue;
                    $user = $repository->getUserByStudentNumber($studentHour->getStudentNumber())[0];
                    $record = new Record();
                    $record->setActivity($batchRecord->getActivity());
                    $record->setCurrentGrade($user->getStudentInfo()->getGrade());
                    $record->setDateCreated($now);
                    $record->setDateFrom($batchRecord->getDateFrom());
                    $record->setDateTo($batchRecord->getDateTo());
                    $record->setEnteredByClub($club);
                    $record->setNotes($batchRecord->getNotes());
                    $record->setNumHours($studentHour->getNumHours());
                    $record->setOrganization($batchRecord->getOrganization());
                    $record->setStudent($user);
                    $record->setSupervisor($batchRecord->getSupervisor());
                    $record->setApprovalStatus(0);
                    $em->persist($record);
                }

                $em->flush();
                
                $batchRecord = new ClubBatchRecord();
                $batchRecord->setDateFrom($now);
                $batchRecord->setDateTo($now);

                foreach($club->getMembers() as $member){
                    $student = new StudentHours();
                    $student->setLastName($member->getLastName());
                    $student->setFirstName($member->getFirstName());
                    $student->setStudentNumber($member->getStudentinfo()->getStudentNumber());
                    $student->setNumHours(0);
                    $batchRecord->addStudentHours($student);
                }

                $form = $this->createForm(new ClubBatchRecordType(), $batchRecord);
                
                return $this->render("MaclayServiceBundle:Club:batchRecord.html.twig", array("error" => "Records Successfully Created", "form" => $form->createView()));
            } catch(\Exception $ee){
                return $this->render("MaclayServiceBundle:Club:batchRecord.html.twig", array("error" => $ee->getMessage(), "form" => $form->createView()));
            }
        }
        
        return $this->render("MaclayServiceBundle:Club:batchRecord.html.twig", array("form" => $form->createView()));
    }
    
    /**
     * The method to get users by their last name.
     * 
     * This method is called by Ajax from the batchEnterRecord page. When a club sponsor types in a last name in to a
     * text box, this method returns a list of students with that last name. If there are multiple students, then there
     * is a dropdown list. If there is only one, then it simply shows the first name.
     * 
     * @param string $lastName The last name of the desired students
     * @return array $users The users matching the last name. 
     */
    public function getStudentsByLastNameAction($lastName){
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("MaclayServiceBundle:User")->getStudentsByLastName($lastName);
        $result = array();
        foreach ($users as $student){
            $result[] = array("firstName" => $student->getFirstName(), "studentNumber" => $student->getStudentinfo()->getStudentNumber());
        }
        
        $response = new Response(json_encode($result));
        $response->headers->set("Content-Type", "application/json");
        return $response;
    }
}
