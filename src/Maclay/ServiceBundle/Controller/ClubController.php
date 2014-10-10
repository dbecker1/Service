<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Maclay\ServiceBundle\Entity\Record;
use Maclay\ServiceBundle\Form\ClubRecordType;

class ClubController extends Controller
{
    public function manageClubAction(){
        $user = $this->getUser();
        
        $clubs = $user->getSponsorForClubs();
        
        foreach ($clubs as $club){
            foreach($club->getMembers() as $member){
                $approvedHours = 0;
                $records = $member->getRecords();
                foreach($records as $record){
                    if ($record->getApprovalStatus() > 0){
                        $approvedHours += $record->getNumHours();
                   }
                }
                $member->setApprovedHours($approvedHours);
            }
        }
        
        return $this->render("MaclayServiceBundle:Club:manage.html.twig", array("clubs" => $clubs, "error" => ""));
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
        
        $students = $user->getSponsorForClubs()[0]->getMembers();
        
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
}
