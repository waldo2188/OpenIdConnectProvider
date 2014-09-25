<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\PasswordFormType;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\AccountFormType;

/**
 * @Route("/account", name="oicp_account")
 */
class AccountController extends Controller
{

    /**
     * @Route("/", name="oicp_account_index")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/change-password/", name="oicp_account_change_password")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function changePasswordAction(Request $request)
    {
        
        $account = clone $this->get('security.context')->getToken()->getUser();
        
        $form = $this->createForm(new PasswordFormType(), $account, array('hasOldpassword' => true));
                
        $form->handleRequest($request);
        
        if($request->isMethod("POST")) {
            if($form->isValid()) {
                $this->get("waldo_oic_enduser.registration")->encodePassword($account);
                
                $em = $this->getDoctrine()->getManager();
                $em->merge($account);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('notice', 'Your password has been changed');

                return $this->redirect($this->generateUrl("oicp_account_change_password"));
            }
        }

        return array('form' => $form->createView());
    }

    

    /**
     * @Route("/edit-profile/", name="oicp_account_edit_profile")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function editProfileAction(Request $request)
    {
        
        $account = $this->get('security.context')->getToken()->getUser();
        
        $form = $this->createForm(new AccountFormType(), $account, array(
            'hasUsernameField' => false,
            'hasPasswordField' => false,
            'validation_groups' => array('edit'),
            ));
                
        $form->handleRequest($request);
        
        if($request->isMethod("POST")) {
            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->merge($account);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('notice', 'Your profile has been changed');

                return $this->redirect($this->generateUrl("oicp_account_edit_profile"));
            }
        }

        return array('form' => $form->createView());
    }

    
   
}
