<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
}
