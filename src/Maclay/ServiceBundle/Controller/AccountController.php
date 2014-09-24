<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends Controller
{
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
                
                $transport = \Swift_SmtpTransport::newInstance('smtp.office365.com', 587, "tls")
                    ->setUsername('maclayservice@maclay.org')
                    ->setPassword('GoMarauders2014')
                    ;

                $mailer = \Swift_Mailer::newInstance($transport);
                
                $name = $user->getFirstName();
                $code = $user->getForgotPasswordCode();

                $body = $this->render("MaclayServiceBundle:Email:forgotPassword.html.twig", array("name" => $name, "code" => $code))->getContent();

                $message = \Swift_Message::newInstance('Password Reset')
                    ->setFrom("maclayservice@maclay.org")
                    ->setReplyTo("maclayservice@maclay.org")
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
    
//    public function groupAction()
//    {
//        $em = $this->getDoctrine()->getEntityManager();
//        
//        $group = new \Maclay\ServiceBundle\Entity\Role("Teacher", array("ROLE_TEACHER"));
//        $em->persist($group);
//        $em->flush();
//        return $this->redirect($this->generateUrl("maclay_service_login"));
//    }
}
