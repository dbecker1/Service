<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
    
    public function addClubMembersAction(Request $request){
        $data = array();
        $form = $this->createFormBuilder($data)
                ->add("members", "textarea")
                ->add("submit", "submit")
                ->getForm();
        
        if ($request->isMethod("POST")){
            try{
                $form->handleRequest($request);
            
                $data = $form->getData();

                $members = explode(",", $data["members"]);
                
                $club = $this->getUser()->getSponsorForClubs()[0];
                
                $toBeAddedUsers = array();
                foreach($members as $memberEmail){
                    $userManager = $this->get("fos_user.user_manager");
                    $user = $userManager->findUserByEmail($memberEmail);
                    if ($user === NULL){
                        throw new \RuntimeException($memberEmail . " could not be found.");
                    }
                    $inClub = false;
                    foreach($user->getClubs() as $userClub){
                        if ($userClub->getId() == $club->getId()){
                            $inClub = true;
                        }
                    }
                    if(!$inClub){
                        $toBeAddedUsers[] = $user;
                    }
                }
                $em = $this->getDoctrine()->getManager();
                foreach($toBeAddedUsers as $user){
                    $user->addClub($club);
                    $em->persist($user);
                }
                $em->flush();
                
                return $this->render("MaclayServiceBundle:Club:addMembers.html.twig", array("error" => "Members successfully added", "form" => $form->createView()));
            } catch (\Exception $ex) {
                return $this->render("MaclayServiceBundle:Club:addMembers.html.twig", array("error" => $ex->getMessage(), "form" => $form->createView()));
            }
            
        }
        else{
            return $this->render("MaclayServiceBundle:Club:addMembers.html.twig", array("error" => "", "form" => $form->createView()));
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
        
        return $this->render("MaclayServiceBundle:Club:newRecord.html.twig", array("error" => "no form", "form" => $form->createView()));
    }
}
