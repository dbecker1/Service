<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Maclay\ServiceBundle\Entity\StudentInfo;

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
            $studentGroup = $em->getRepository("MaclayServiceBundle:Role")->find(7);
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
        
        return $this->render("MaclayServiceBundle:Admin:upload.html.twig", array("newUsers" => true, "error" => "success"));

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
}
