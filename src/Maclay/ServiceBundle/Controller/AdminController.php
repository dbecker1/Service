<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
            $em = $this->getDoctrine();
            $studentGroup = $em->getRepository("MaclayServiceBundle:Role")->find(7);
            foreach($students as $student){
                if($student === NULL){
                    continue;
                }
                $user = $userManager->createUser();
                $user->setUsername($student[0]);
                $user->setPlainPassword("test1234");
                $user->setEnabled(1);
                $user->setEmail($student[4]);
                $user->addGroup($studentGroup);
                $userManager->updateUser($user);
            }
        } catch (\Exception $ex) {
            return $this->render("MaclayServiceBundle:Admin:upload.html.twig", array("newUsers" => true, "error" => $ex->getMessage()));
        }
        
        return $this->render("MaclayServiceBundle:Admin:upload.html.twig", array("newUsers" => true, "error" => "success"));

    }
}
