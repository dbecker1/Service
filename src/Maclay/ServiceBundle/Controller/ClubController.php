<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
}
