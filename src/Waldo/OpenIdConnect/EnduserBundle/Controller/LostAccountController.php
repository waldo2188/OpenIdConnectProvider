<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\LostAccountFormType;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\PasswordFormType;
use Symfony\Component\Form\FormError;

/**
 * @Route("/lost-account", name="oicp_lost_account")
 */
class LostAccountController extends Controller
{

    /**
     * @Route("/", name="oicp_lost_account_search")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function searchAction(Request $request)
    {
        $isFound = null;
        $form = $this->createForm(new LostAccountFormType());

        $form->handleRequest($request);
        
        if($request->isMethod("POST")) {
            if($form->isValid()) {
                if($form->get('cancel')-> isClicked()) {
                    return $this->redirect($this->generateUrl("login"));
                }
                
                if($form->get('username')->getData() !== null) {
                    $isFound = $this->get('waldo_oic_p.account_actions')->handleLostPassword($form->get('username')->getData());
                
                    if($isFound === true) {
                        return $this->redirect($this->generateUrl("oicp_lost_account_email_sent"));
                    }
                    
                } else {                
                    $form->get('username')->addError(new FormError("Please, fill the search field"));
                }
            }
        }
        
        return $this->render("WaldoOpenIdConnectEnduserBundle:LostAccount:search.html.twig", array(
            'form' => $form->createView(),
            'isFound' => $isFound
        ));
    }


    /**
     * @Route("/email-sent/", name="oicp_lost_account_email_sent")
     */
    public function emailSentAction()
    {
        return $this->render("WaldoOpenIdConnectEnduserBundle:LostAccount:emailSent.html.twig");
    }

    /**
     * @Route("/change-password/{token}", name="oicp_lost_account_change_password")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function changePasswordAction(Request $request, $token)
    {
        $accountAction = $this->get("waldo_oic_p.account_actions")->isValidLostPasswordToken($token);
        
        $account = $accountAction ? $accountAction->getAccount() : null;
        
        $form = $this->createForm(new PasswordFormType(), $account);
        
        $form->handleRequest($request);
        
        if($request->isMethod("POST")) {
            if($form->isValid()) {
                $this->get("waldo_oic_enduser.registration")->encodePassword($account);
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($account);
                $em->remove($accountAction);
                $em->flush();
                
                return $this->redirect($this->generateUrl("oicp_lost_account_password_changed"));
            }
        }

        return $this->render("WaldoOpenIdConnectEnduserBundle:LostAccount:changePassword.html.twig",  array(
            'form' => $form->createView(),
            "isValid" => $accountAction
            ));
    }
    
    /**
     * @Route("/password-changed/", name="oicp_lost_account_password_changed")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function passwordChangedAction()
    {
        return $this->render("WaldoOpenIdConnectEnduserBundle:LostAccount:passwordChanged.html.twig");
    }
}
