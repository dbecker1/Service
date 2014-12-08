<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This controller contains the method that redirects after login
 */
class ProfileController extends Controller
{
    /**
     * The method for redirecting after login.
     * 
     * This method is redirected to after a user logs in, and then redirects the user based on if they need to change 
     * their password or their role.
     */
    public function profileAction()
    {
        //Check to see if this is the first time they have signed in
        $user = $this->getUser();
        if($user->getTempPass()){
            $encoder = $this->get("security.encoder_factory")->getEncoder($user);
            $encodedPass = $encoder->encodePassword($user->getTempPass(), $user->getSalt());
            if ($encodedPass === $user->getPassword()){
                return $this->redirect($this->generateUrl('default', array("controller" => "Account", "action" => "ChangePassword")));
            }
            else {
                $user->setTempPass("");
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        $securityContext = $this->container->get("security.context");
        if($securityContext->isGranted("ROLE_ADMIN"))
        {
            return $this->redirect($this->generateUrl('default', array("controller" => "Admin", "action" => "UploadStudents")));
        }
        else if($securityContext->isGranted("ROLE_COORDINATOR"))
        {
            return $this->redirect($this->generateUrl('default', array("controller" => "Record", "action" => "PendingRecords")));
        }
        else if($securityContext->isGranted("ROLE_STUDENT"))
        {
            return $this->redirect($this->generateUrl('default', array("controller" => "Record", "action" => "RecordSummary")));
        }
        else if($securityContext->isGranted("ROLE_CLUBSPONSOR"))
        {
            return $this->redirect($this->generateUrl('default', array("controller" => "Club", "action" => "ManageClub")));
        }
         else if($securityContext->isGranted("ROLE_SCHOOLADMIN"))
        {
            return $this->redirect($this->generateUrl('default', array("controller" => "Record", "action" => "StudentHistory")));
        }
        return $this->render("MaclayServiceBundle:Profile:profile.html.twig");
    }
    
    
}
