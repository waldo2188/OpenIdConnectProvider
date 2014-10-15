<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Waldo\OpenIdConnect\EnduserBundle\Events\OICProviderEvent;
use Waldo\OpenIdConnect\EnduserBundle\Events\AccountEvent;

/**
 * @Route("/registration", name="oicp_registration")
 */
class RegistrationController extends Controller
{

    /**
     * @Route("/new-account/", name="oicp_registration_new_account")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function registerAction(Request $request)
    {
        /* @var $userRegistration \Waldo\OpenIdConnect\EnduserBundle\Services\RegistrationUserService */
        $userRegistration = $this->get("waldo_oic_enduser.registration");

        $user = $userRegistration->getBlankUser();

        $form = $this->createForm($userRegistration->getFormType(), $user, array('hasProfileField' => false));

        $form->handleRequest($request);

        if ('POST' === $request->getMethod()) {
            if ($form->isValid()) {
                $userRegistration->encodePassword($user);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $accountEvent = new AccountEvent();
                $accountEvent->setAccount($user);

                $this->get("event_dispatcher")->dispatch(
                        OICProviderEvent::AFTER_SAVE_ACCOUNT, $accountEvent);
                
                return $this->redirect($this->generateUrl("oicp_registration_done"));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/created-account/", name="oicp_registration_done")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function registrationDoneAction()
    {
        return array();
    }

    /**
     * @Route("/valid-account/{token}", name="oicp_registration_account_validation")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function accountValidationAction($token)
    {
        $user = $this->get("waldo_oic_enduser.registration")->handleValidationToken($token);
        
        return array("user" => $user);
    }

    /**
     * @Route("/valid-new-email/{token}", name="oicp_registration_account_new_email")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function accountNewEmailAction($token)
    {
        $isValid = $this->get("waldo_oic_enduser.registration")->handleNewEmailToken($token);
        
        if($isValid === false) {
            throw $this->createNotFoundException();
        }
        
        return array("isValid" => $isValid);
    }

}
