<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\HttpFoundation\Request;

/**
 * The controller for account methods.
 * 
 * This controller is used for basic account functions: logging in, changing password, forgot password, etc.
 */
class AccountController extends Controller
{
    /**
     * This method checks if a user is logged in and then redirects. 
     * 
     * Since I used the FOSUserBundle for managing log in and users, this method simply checks if a user is logged in
     * and redirets them to the correct place
     */
    public function loginAction()
    {
        $securityContext = $this->container->get("security.context");
        if ($securityContext->isGranted("IS_AUTHENTICATED_REMEMBERED")){
            return $this->redirect($this->generateUrl("maclay_service_profile"));
        }
        else {
            return $this->redirect($this->generateUrl("fos_user_security_login"));
        }
    }
    
    /**
     * This method sends an email to a user with a password reset link.
     * 
     * This method takes an email address, checks for a user with that email address, creates a code that will be sent
     * to the user, and then emails the user
     * 
     * @param Request $request The form that contains the user's email.
     */
    public function forgotPasswordAction(Request $request)
    {
        try{
            $data = array();
            
            $form = $this->createFormBuilder($data)
                ->add("email", "text")
                ->add("submit", "submit")
                ->getForm();
            
            $form->handleRequest($request);
            
            if($form->isValid()){
                $data = $form->getData();
                
                $usermanager = $this->get("fos_user.user_manager");
                $user = $usermanager->findUserByEmail($data["email"]);
                
                if($user === NULL)
                    throw new \RuntimeException("User with that email does not exist");
                
                $generator = new SecureRandom();
                $user->setForgotPasswordCode(bin2hex($generator->nextBytes(20)));
                
                if (in_array(@$_SERVER['REMOTE_ADDR'], array(
                    '127.0.0.1',
                    '::1',
                ))) {
                    $transport = \Swift_SmtpTransport::newInstance('smtp.office365.com', 25, "tls")
                    ->setUsername('maclayservice@maclay.org')
                    ->setPassword('GoMarauders2014')
                    ;
                }
                else{
                    $transport = \Swift_SmtpTransport::newInstance('localhost');
                }
                
                $mailer = \Swift_Mailer::newInstance($transport);
                
                $name = $user->getFirstName();
                $code = $user->getForgotPasswordCode();
                $username = $user->getUsername();

                $body = $this->render("MaclayServiceBundle:Email:forgotPassword.html.twig", array("name" => $name, "code" => $code, "username" => $username))->getContent();

                $message = \Swift_Message::newInstance('Password Reset')
                    ->setFrom(array("maclayservice@maclay.org" => "Maclay School Community Service"))
                    ->setReplyTo(array("maclayservice@maclay.org" => "Maclay School Community Service"))
                    ->setTo($user->getEmail())
                    ->setBody($body, "text/html")
                    ;

                $mailer->send($message);
                
                $usermanager->updateUser($user);
                
                return $this->render("MaclayServiceBundle:Account:forgotPassword.html.twig", array("error" => "An email with a password reset link has been sent to " . $user->getEmail(), "form" => $form->createView()));
            }
            else{
                return $this->render("MaclayServiceBundle:Account:forgotPassword.html.twig", array("error" => "", "form" => $form->createView()));
            }
        } catch(\Exception $ee){
            return $this->render("MaclayServiceBundle:Account:forgotPassword.html.twig", array("error" => $ee->getMessage(), "form" => $form->createView()));
        }
    }
    
    /**
     * The method for resetting a user's password
     * 
     * This method is the second half of the forgot password workflow. The link sent to the user contains a code
     * that this method compares against the users in the database and then lets them reset their password.
     * 
     * @param Request $request The form that contains the user's new password.
     * @param string $code The reset key that was included in the link sent to the user.
     */
    public function resetPasswordAction(Request $request, $code = null){
        $data = array();
            
        $form = $this->createFormBuilder($data)
            ->add("newPassword", "password")
            ->add("confirmNewPassword", "password")
            ->add("submit", "submit")
            ->getForm();
        try{
            $form->handleRequest($request);
            
            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository("MaclayServiceBundle:User");
            if($code !== NULL)
                $user = $userRepo->findOneByForgotPasswordCode($code);
            else
                throw new \RuntimeException("No invite code.");
            
            if($user === NULL)
                throw new \RuntimeException("No user found.");
            
            if($form->isValid()){
                $data = $form->getData();
                
                if (!StringUtils::equals($data["newPassword"], $data["confirmNewPassword"]))
                    throw new \RuntimeException("Passwords do not match");
                
                $user->setPlainPassword($data["newPassword"]);
                $user->setForGotPasswordCode(NULL);
                $user->setTempPass("");
                $em->persist($user);
                $em->flush();
                
                 return $this->redirect($this->generateUrl("maclay_service_login"));
            }
            else{
                return $this->render("MaclayServiceBundle:Account:resetPassword.html.twig", array("code" => $code, "form" => $form->createView()));
            }
        } catch(\Exception $ee){
            return $this->render("MaclayServiceBundle:Account:resetPassword.html.twig", array("error" => $ee->getMessage(), "code" => $code, "form" => $form->createView()));
        }
    }
    
    /**
     * The method for changing a user's password
     * 
     * I didn't like the change password that came with FOSUserBundle since it didn't look good with my template, 
     * so I made my own.
     * 
     * @param Request $request The form containing the user's old password and new password.
     */
    public function changePasswordAction(Request $request){
        $data = array();
        $form = $this->createFormBuilder($data)
                ->add("oldPassword", "password")
                ->add("newPassword", "password")
                ->add("confirmNewPassword", "password")
                ->add("submit", "submit")
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isValid()){
            $userManager = $this->container->get('fos_user.user_manager');

            $user = $userManager->findUserByUsername($this->getUser()->getUsername());
            
            $data = $form->getData();
                
            try{
                $encoder_service = $this->get('security.encoder_factory');
                $encoder = $encoder_service->getEncoder($user);
                $encoded_pass = $encoder->encodePassword($data["oldPassword"], $user->getSalt());

                if ($user->getPassword() != $encoded_pass)
                    throw new \RuntimeException("Old password is incorrect.");

                if (!StringUtils::equals($data["newPassword"], $data["confirmNewPassword"]))
                    throw new \RuntimeException("New passwords do not match");
                
                $user->setPlainPassword($data["newPassword"]);
                
                $userManager->updateUser($user);
                
                return $this->redirect($this->generateUrl('default', array("controller" => "Record", "action" => "RecordSummary")));
            }
            catch(\Exception $ee){
                return $this->render("MaclayServiceBundle:Account:changePassword.html.twig", array("form" => $form->createView(), "error" => $ee->getMessage()));
            }
            
        }
        return $this->render("MaclayServiceBundle:Account:changePassword.html.twig", array("form" => $form->createView()));
    }
    /**
     * This method create user roles (groups).
     * 
     * This method can be used to create groups of users. If you look in the databse under roles, you can see the 
     * output of this method. This method really isn't needed anymore, so I commented it out.
     */
    public function groupAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $group = new \Maclay\ServiceBundle\Entity\Role("Teacher", array("ROLE_TEACHER"));
        $em->persist($group);
        $em->flush();
        return $this->redirect($this->generateUrl("maclay_service_login"));
    }
}
