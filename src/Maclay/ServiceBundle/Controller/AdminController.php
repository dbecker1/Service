<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Maclay\ServiceBundle\Entity\StudentInfo;
use Maclay\ServiceBundle\Form\ClubType;
use Maclay\ServiceBundle\Entity\Club;
use Maclay\ServiceBundle\Entity\Record;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The controller used for admin functions.
 * 
 * This controller is used for admin purposes such as uploading students and creating clubs
 */
class AdminController extends Controller
{
    /**
     * This method is used for uploading users.
     * 
     * WARNING: Before using this method, email Daniel Becker to make sure your csv file is formatted correctly.
     * This method takes in a CSV file, parses it, and then creates the user accounts out of it. This method DOES 
     * NOT send them their invite emails.
     * 
     * @param file $file Not actually a parameter, but worth noting that a file must be in the body of the POST request.
     */
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
    
    /**
     * This method is used to randomly generate a temporary password for new users.
     * 
     * @return string $password The temporary password.
     */
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
    
    /**
     * The method for creating new clubs.
     * 
     * This method is used for creating new clubs. This is the only Admin method that the coordinator has access to.
     * 
     * @param Request $request The form containing the new club and user's emails.
     */
    public function createClubAction(Request $request)
    {
        $form = $this->createForm(new ClubType(), new Club());
         
        $form->handleRequest($request);
        
        try{
            if($form->isValid())
            {
                $club = $form->getData();
                $emails = $form->get("sponsorEmail")->getData();
                $em = $this->getDoctrine()->getManager();
                $sponsorGroup = $em->getRepository("MaclayServiceBundle:Role")->findOneByName("ClubSponsor");
                $sponsors = explode(",", $emails);
                $userManager = $this->get("fos_user.user_manager");
                foreach ($sponsors as $sponsorEmail){
                    $sponsor = $userManager->findUserByEmail($sponsorEmail);
                    if ($sponsor === NULL){
                        $sponsor = $userManager->createUser();
                        $sponsor->setUsername(substr($sponsorEmail, 0,strpos($sponsorEmail, "@")));
                        $randomPass = $this->randomPassword();
                        $sponsor->setPlainPassword($randomPass);
                        $sponsor->setTempPass($randomPass);
                        $sponsor->setEnabled(1);
                        $sponsor->setEmail($sponsorEmail);
                        $sponsor->setFirstName("");
                        $sponsor->setMiddleName("");
                        $sponsor->setLastName("");
                        $sponsor->addGroup($sponsorGroup);
                        $sponsor->setIsInvited(false);
                        $userManager->updateUser($sponsor);
                    }
                    else{
                        $sponsor->addGroup($sponsorGroup);
                    }
                    $club->addSponsor($sponsor);
                }
                
                $em->persist($club);

                $em->flush();
                return $this->render("MaclayServiceBundle:Admin:createClub.html.twig", array("error" => "Club Successly Created", "form" => $form->createView()));
            }
        } catch (\Exception $ex) {
            return $this->render("MaclayServiceBundle:Admin:createClub.html.twig", array("error" => $ex->getMessage(), "form" => $form->createView()));
        }
        
        
        return $this->render("MaclayServiceBundle:Admin:createClub.html.twig", array("error" => "", "form" => $form->createView()));
    }
    
    /**
     * The method for emailing users who haven't been invited yet.
     * 
     * WARNING: Using this method doesn't work that well on GoDaddy's server for some reason that I do not know. 
     *          That is the reason for the message on the log in screen.
     * This method provides the admin with a list of types of users to email and then sends out the invite email.
     * 
     * @param Request $request The request object. Simply used for determining GET vs. POST
     */
    public function emailUninvitedUsersAction(Request $request){
        
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
                        if ($user->hasRole("ROLE_STUDENT" == false)){
                            $emailUsers[] = $user;
                        }
                    }
                }
                else{
                    $emailUsers = $uninvitedUsers;
                }
                
                set_time_limit(600);
                
                $container = $this->container;
                
                if (in_array(@$_SERVER['REMOTE_ADDR'], array(
                    '127.0.0.1',
                    '::1',
                ))) {
                    $transport = \Swift_SmtpTransport::newInstance('smtp.office365.com', 587, "tls")
                    ->setUsername($container->getParameter("emailAddress"))
                    ->setPassword($container->getParameter("emailPassword"))
                    ;
                }
                else{
                    $transport = \Swift_SmtpTransport::newInstance('localhost');
                }
                
                $mailer = \Swift_Mailer::newInstance($transport);

                foreach($emailUsers as $user){
                    $username = $user->getUsername();
                    $password = $user->getTempPass();
                    $name = $user->getFirstName();

                    $isOther = false;
                    if ($user->getStudentInfo() === NULL){
                        $isOther = true;
                    }
                    
                    $isStudent = $user->hasRole("ROLE_STUDENT");
                    $isSchoolAdmin = $user->hasRole("ROLE_SCHOOLADMIN");
                    $isClubSponsor = $user->hasRole("ROLE_COORDINATOR");
                    $body = $this->render("MaclayServiceBundle:Email:inviteUser.html.twig", array("username" => $username, "password" => $password, "name" => $name, "isClubSponsor" => $isClubSponsor, "isSchoolAdmin" => $isSchoolAdmin, "isStudent" => $isStudent, "developerName" => $container->getParameter("applicationDeveloper"), "emailLink" => $container->getParameter("emailAddress")))->getContent();

                    $message = \Swift_Message::newInstance('Begin Using Maclay Community Service')
                        ->setFrom(array($container->getParameter("emailAddress") => "Maclay School Community Service"))
                        ->setReplyTo(array($container->getParameter("emailAddress") => "Maclay School Community Service"))
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
    
    /**
     * The method for getting a count of uninvited users by grade.
     * 
     * This method is called from Ajax on the emailUninvitedUsers page. When the dropdown is changed, it calls this
     * method which returns the number of users of that type.
     * 
     * @param int $grade The grade of the desired users. -1 for non-students.
     * @return int $count The count of uninvited users in the grade.
     */
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
    
    /**
     * The method for importing previous records.
     * 
     * There should theoretically be no use for the following method. It was used in the beginning to import all of 
     * the previous records that students had into the system. 
     * 
     * @param file $file Not a parameter, but worth noting that a file must be in the body of the POST request.
     */
    public function importPreviousRecordsAction(){
        $file;
        ini_set('auto_detect_line_endings', true);
        try
        {
            $file = $_FILES["file"];
            if (!isset($file["error"]) || is_array($file["error"])){
                throw new \RuntimeException("");
            }
        } 
        catch (\Exception $ex) 
        {
            return $this->render("MaclayServiceBundle:Admin:importPreviousRecords.html.twig");
        }
        
        $path = $this->container->getParameter("importUploadDirectory");
        $error = json_decode($this->forward("maclay.filecontroller:uploadFileAction", array('file' => $file, "path" => $path, "import" => true, "upload" => false))->getContent(), true);
        if (strlen($error["error"]) > 0){
            return $this->render("MaclayServiceBundle:Admin:importPreviousRecords.html.twig", array("error" => $error["error"]));
        }
        
        try{
            set_time_limit(600);
            $updates = false;
            try{
                $check = $_POST["updates"] == "checked";
                $updates = true;
            }
            catch (\Exception $ee){
                $updates = false;
            }
            $records = array();
            if (($handle = fopen($path . $error["fileName"], "r")) !== FALSE) {
                while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
                    $records[] = $data;
                }
                fclose($handle);
            }
            else{
                throw new \RuntimeException("Unable to open file for parsing");
            }
            
            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository("MaclayServiceBundle:User");
            $recordRepo = $em->getRepository("MaclayServiceBundle:Record");
            $failedUsers = array();
            
            foreach($records as $record){
                try{
                    $students = $userRepo->getUserByStudentNumber($record[1]);
                    $student = $students[0];
                    if ($student === NULL){
                        throw new \RuntimeException("");
                    }
                    $counts = array();
                    try{
                        $counts[] = $record[2];
                        $counts[] = $record[3];
                        $counts[] = $record[4];
                        $counts[] = $record[5];
                    }
                    catch (\Exception $ee){
                        
                    }
                    
                    $grade = 9;
                    $studentGrade = $student->getStudentinfo()->getGrade();
                    foreach($counts as $count){
                        if ($count == 0){
                            $grade++;
                            continue;
                        }
                        if ($updates){
                            $studentRecords = $recordRepo->getRecordByGrade($student, $grade);
                            if ($studentRecords != NULL){
                                $studentRecord = $studentRecords[0];
                                $studentRecord->setNumHours($studentRecord->getNumHours() + $count);
                                $grade++;
                                continue;
                            }
                        }
                        $studentRecord = new Record();
                        $studentRecord->setCurrentGrade($grade);
                        if($studentGrade - $grade == 3){
                            $studentRecord->setDateFrom(new \DateTime("10-01-2011"));
                            $studentRecord->setDateTo(new \DateTime("5-01-2012"));
                        }
                        else if ($studentGrade - $grade == 2){
                            $studentRecord->setDateFrom(new \DateTime("10-01-2012"));
                            $studentRecord->setDateTo(new \DateTime("5-01-2013"));
                        }
                        else if ($studentGrade - $grade == 1) {
                            $studentRecord->setDateFrom(new \DateTime("10-01-2013"));
                            $studentRecord->setDateTo(new \DateTime("5-01-2014"));
                        }
                        else {
                            $studentRecord->setDateFrom(new \DateTime("10-01-2014"));
                            $studentRecord->setDateTo(new \DateTime("5-01-2015"));
                        }
                        $studentRecord->setNumHours($count);
                        $studentRecord->setActivity($grade . "th Grade Hours");
                        $studentRecord->setNotes("This record contains all of the service hours from the given grade.");
                        $studentRecord->setOrganization("Maclay");
                        $studentRecord->setSupervisor("Heather Bas");
                        $now = new \DateTime("now");
                        $studentRecord->setDateCreated($now);
                        $studentRecord->setApprovalDate($now);
                        $studentRecord->setApprovalStatus(1);
                        $studentRecord->setStudent($student);
                        
                        $em->persist($studentRecord);
                        
                        $grade++;
                    }
                }
                catch (\Exception $ee){
                    $failedUsers[] = $record[0];
                }
            }
            $em->flush();
            
            if (count($failedUsers) > 0){
                $error = "Import succeeded except for the following users: ";
                foreach($failedUsers as $failedUser){
                    $error = $error . $failedUser . ", ";
                }
                return $this->render("MaclayServiceBundle:Admin:importPreviousRecords.html.twig", array("error" => $error));
            }
            
            return $this->render("MaclayServiceBundle:Admin:importPreviousRecords.html.twig", array("error" => "Success."));
        }
        catch(\Exception $ee){
            return $this->render("MaclayServiceBundle:Admin:importPreviousRecords.html.twig", array("error" => $ee->getMessage()));
        }
         
        
    }
    
    /**
     * The method for creating school admins. 
     * 
     * This method process a list of email addresses of school admins and then creates accounts for them. This method
     * DOES NOT send them their invite emails. 
     * 
     * @param Request $request The form containing the list of school admins.
     */
    public function createSchoolAdminsAction(Request $request){
        $data = array();
        
        $form = $this->createFormBuilder($data)
                ->add("emailAddresses", "textarea")
                ->add("submit", "submit")
                ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isValid()){
            try{
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $userManager = $this->get("fos_user.user_manager");
                $adminGroup = $em->getRepository("MaclayServiceBundle:Role")->findOneByName("SchoolAdmin");
                foreach(explode(",", $data["emailAddresses"]) as $email){
                    $admin = $userManager->findUserByEmail($email);
                    if ($admin === NULL){
                        $admin = $userManager->createUser();
                        $admin->setUsername(substr($email, 0,strpos($email, "@")));
                        $randomPass = $this->randomPassword();
                        $admin->setPlainPassword($randomPass);
                        $admin->setTempPass($randomPass);
                        $admin->setEnabled(1);
                        $admin->setEmail($email);
                        $admin->setFirstName("");
                        $admin->setMiddleName("");
                        $admin->setLastName("");
                        $admin->addGroup($adminGroup);
                        $admin->setIsInvited(false);
                    }
                    else{
                        $admin->addGroup($adminGroup);
                    }
                    $userManager->updateUser($admin);
                }

                return $this->render("MaclayServiceBundle:Admin:createSchoolAdmins.html.twig", array("form" => $form->createView(), "error" => "School Admins successfully created."));
            } catch(\Exception $ee){
                return $this->render("MaclayServiceBundle:Admin:createSchoolAdmins.html.twig", array("form" => $form->createView(), "error" => $ee->getMessage()));
            }
        }
        return $this->render("MaclayServiceBundle:Admin:createSchoolAdmins.html.twig", array("form" => $form->createView()));
    }
    
    public function exportRecordsByGradeAction(Request $request){
        $data = array();
        
        $form = $this->createFormBuilder($data)
                ->add("grade", "choice", array(
                    "choices" => array("12" => "12", "11" => "11", "10" => "10", "9" => "9"),
                ))
                ->add("submit", "submit")
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isValid()){
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository("MaclayServiceBundle:User")->getUsersByGrade($data["grade"]); 
            try{
                foreach($users as $user){
                    $approvedHours = 0;
                    $records = $user->getRecords();
                    foreach($records as $record){
                        if ($record->getApprovalStatus() > 0){
                            $approvedHours =+ $record->getNumHours();
                       }
                    }
                    $user->setApprovedHours($approvedHours);
                    $name = $user->getLastName();
                    if(strpos($name, "1") !== false){
                        $newName = substr($name, 0, strlen($name) -1);
                        $user->setLastName($newName);
                    }
                }
            } catch (Exception $ex) {
                return $this->render("MaclayServiceBundle:Admin:exportRecordsByGrade.html.twig", array("form" => $form->createView(), "error" => "Error calculating hours: ". $ex->getMessage()));
            }

            try{
                $response = new StreamedResponse();
                $response->setCallback(function() use ($users){
                    $handle = fopen('php://output', 'w+');

                    fputcsv($handle, array('Last Name', 'First Name', 'Hours'),',');

                    foreach($users as $user){
                        fputcsv($handle,array($user->getLastName(), $user->getFirstName(), $user->getApprovedHours()),',');
                    }

                    fclose($handle);
                });

                $response->setStatusCode(200);
                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

                return $response;
            } catch (Exception $ex) {
                return $this->render("MaclayServiceBundle:Admin:exportRecordsByGrade.html.twig", array("form" => $form->createView(), "error" => "Error exporting hours: ". $ex->getMessage()));
            }
        }
        else{
            return $this->render("MaclayServiceBundle:Admin:exportRecordsByGrade.html.twig", array("form" => $form->createView()));
        }
    }
    
    /**
     * The method for removing the blank spaces of user's last names in the database. 
     * 
     * When the users were first imported in to the system, they randomly had new lines before their last names.
     * This affected the ability to search for a user by their last name. This method gets all of the users in the
     * database, removes the spaces at the beginning and end of their last name, and then save it to the database.
     */
    public function removeLineFromLastNameAction(){
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("MaclayServiceBundle:User")->findAll();
        foreach ($users as $user){
            $lastName = trim($user->getLastName());
            $user->setLastName($lastName);
        }
        $em->flush();
        $response = new Response();                                         
        $response->headers->set('Content-type', 'application/json; charset=utf-8');
        $response->setContent("success");
        
        return $response;
    }
    
    /**
     * This method returns the documentation for the website.
     */
    public function documentationAction(){
        if (in_array(@$_SERVER['REMOTE_ADDR'], array(
            '127.0.0.1',
            '::1',
        ))) {
            return $this->redirect("http://localhost/Documentation");
        }
        else{
            return $this->redirect("http://www.maclayservice.org/Documentation");
        }
    }
}