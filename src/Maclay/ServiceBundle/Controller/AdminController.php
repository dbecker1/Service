<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Maclay\ServiceBundle\Entity\StudentInfo;
use Maclay\ServiceBundle\Form\ClubType;
use Maclay\ServiceBundle\Entity\Club;

class AdminController extends Controller
{
    public function uploadStudentsAction()
    {
        $file;
        try
        {
            $file = $_FILES["uploadedUsers"];
        } 
        catch (\Exception $ex) 
        {
            return $this->render("MaclayServiceBundle:Admin:upload.html.twig", array("newUsers" => true, "error" => ""));
        }
        
        
        $path = $this->container->getParameter("importUploadDirectory");
        $error = json_decode($this->forward("maclay.filecontroller:uploadFileAction", array('file' => $file, "path" => $path, "import" => true, "upload" => false))->getContent(), true);
        if (strlen($error["error"]) > 0){
            return $this->render("MaclayServiceBundle:Admin:upload.html.twig", array("newUsers" => true, "error" => $error["error"]));
        }
        
        try{
            set_time_limit(600);
            $students = array();
            if (($handle = fopen($path . $error["fileName"], "r")) !== FALSE) {
                while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
                    $students[] = $data;
                }
                fclose($handle);
            }
            else{
                throw new \RuntimeException("Unable to open file for parsing");
            }
            $userManager = $this->get("fos_user.user_manager");
            $em = $this->getDoctrine()->getManager();
            $studentGroup = $em->getRepository("MaclayServiceBundle:Role")->findOneByName("Student");
            foreach($students as $student){
                if($student === NULL){
                    continue;
                }
                $user = $userManager->createUser();
                $existingUser = $userManager->findUserByUsername($student[8]);
                if ($existingUser !== NULL){
                    $studentInfo = $existingUser->getStudentInfo();
                    $studentInfo->setGrade($student[5]);
                }
                else{
                    $user->setUsername($student[8]);
                    $randomPass = $this->randomPassword();
                    $user->setPlainPassword($randomPass);
                    $user->setTempPass($randomPass);
                    if($student[5] == 8){
                        $user->setEnabled(0);
                    }
                    else{
                        $user->setEnabled(1);
                    }
                    $user->setEmail($student[9]);
                    $user->setFirstName($student[1]);
                    $user->setMiddleName($student[2]);
                    $user->setLastName($student[0]);
                    $user->addGroup($studentGroup);
                    $user->setIsInvited(false);
                    $userManager->updateUser($user);
                    $info = new StudentInfo();
                    $info->setGender($student[6]);
                    $info->setGradYear($student[7]);
                    $info->setGrade($student[5]);
                    $info->setStudent($user);
                    $info->setStudentNumber($student[4]);
                    $em->persist($info);
                }
            }
            $em->flush();
        } catch (\Exception $ex) {
            return $this->render("MaclayServiceBundle:Admin:upload.html.twig", array("newUsers" => true, "error" => $ex->getMessage()));
        }
        
        return $this->render("MaclayServiceBundle:Admin:upload.html.twig", array("newUsers" => true, "error" => "Students Successfully Uploaded"));

    }
    
    public function randomPassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); 
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
    
    public function createClubAction(Request $request)
    {
        $form = $this->createForm(new ClubType(), new Club());
         
        $form->handleRequest($request);
        
        try{
            if($form->isValid())
            {
                $club = $form->getData();
                $email = $form->get("sponsorEmail")->getData();
                $em = $this->getDoctrine()->getManager();
                $sponsorGroup = $em->getRepository("MaclayServiceBundle:Role")->findOneByName("ClubSponsor");
                $userManager = $this->get("fos_user.user_manager");
                $sponsor = $userManager->findUserByEmail($email);
                if ($sponsor === NULL){
                    return $this->render("MaclayServiceBundle:Admin:createClub.html.twig", array("error" => "Sponsor account does not exist", "form" => $form->createView()));
                }
                $sponsor->addGroup($sponsorGroup);
                $club->addSponsor($sponsor);
                $em->persist($club);

                $em->flush();
                return $this->render("MaclayServiceBundle:Admin:createClub.html.twig", array("error" => "Club Successly Created", "form" => $form->createView()));
            }
        } catch (\Exception $ex) {
            return $this->render("MaclayServiceBundle:Admin:createClub.html.twig", array("error" => $ex->getMessage(), "form" => $form->createView()));
        }
        
        
        return $this->render("MaclayServiceBundle:Admin:createClub.html.twig", array("error" => "", "form" => $form->createView()));
    }
    
    public function emailUninvitedUsersAction(Request $request){
        return $this->render("MaclayServiceBundle:Admin:emailUninvitedUsers.html.twig", array("count" => 4, "error" => "test"));
        $em = $this->getDoctrine()->getManager();
        $uninvitedUsers = $em->getRepository("MaclayServiceBundle:User")->getUninvitedUsers();
        
        if ($request->getMethod() == "GET"){
            return $this->render("MaclayServiceBundle:Admin:emailUninvitedUsers.html.twig", array("count" => count($uninvitedUsers), "error" => ""));
        }
        else{
            try{
                $grade = $_POST["userGrade"];
                $emailUsers = array();
                
                if ($grade > 0){
                    foreach($uninvitedUsers as $user){
                        $studentInfo = $user->getStudentInfo();
                        if ($studentInfo != NULL && $studentInfo->getGrade() == $grade){
                            $emailUsers[] = $user;
                        }
                    }
                }
                else if ($grade == -1){
                    foreach($uninvitedUsers as $user){
                        if ($user->getStudentInfo() === NULL){
                            $emailUsers[] = $user;
                        }
                    }
                }
                else{
                    $emailUsers = $uninvitedUsers;
                }
                
                set_time_limit(600);
                $transport = \Swift_SmtpTransport::newInstance('smtp.office365.com', 587, "tls")
                    ->setUsername('maclayservice@maclay.org')
                    ->setPassword('GoMarauders2014')
                    ;

                $mailer = \Swift_Mailer::newInstance($transport);

                foreach($emailUsers as $user){
                    $username = $user->getUsername();
                    $password = $user->getTempPass();
                    $name = $user->getFirstName();

                    $body = $this->render("MaclayServiceBundle:Email:inviteUser.html.twig", array("username" => $username, "password" => $password, "name" => $name))->getContent();

                    $message = \Swift_Message::newInstance('Begin Using Maclay Community Service')
                        ->setFrom("maclayservice@maclay.org")
                        ->setReplyTo("maclayservice@maclay.org")
                        ->setTo($user->getEmail())
                        ->setBody($body, "text/html")
                        ;

                    $mailer->send($message);

                    $user->setIsInvited(true);
                    $em->persist($user);
                }

                $em->flush();

                $uninvitedUsers = $em->getRepository("MaclayServiceBundle:User")->getUninvitedUsers();

                return $this->render("MaclayServiceBundle:Admin:emailUninvitedUsers.html.twig", array("count" => count($uninvitedUsers), "error" => "Users successfully emailed"));
            }
            catch (\Exception $ee){
                return $this->render("MaclayServiceBundle:Admin:emailUninvitedUsers.html.twig", array("count" => count($uninvitedUsers), "error" => $ee->getMessage()));
            }
            
        }
        
    }
    
    public function getUninvitedUserCountAction($grade){
        $em = $this->getDoctrine()->getManager();
        $uninvitedUsers = $em->getRepository("MaclayServiceBundle:User")->getUninvitedUsers();
        
        $userCount = 0;
        if ($grade > 0){
            foreach($uninvitedUsers as $user){
                $studentInfo = $user->getStudentInfo();
                if ($studentInfo != NULL && $studentInfo->getGrade() == $grade){
                    $userCount++;
                }
            }
        }
        else if ($grade == -1){
            foreach($uninvitedUsers as $user){
                if ($user->getStudentInfo() === NULL){
                    $userCount++;
                }
            }
        }
        else{
            $userCount = count($uninvitedUsers);
        }
        
        $count["count"] = $userCount;
        $response = new Response();                                         
        $response->headers->set('Content-type', 'application/json; charset=utf-8');
        $response->setContent(json_encode($count));
        
        return $response;
    }
}
