<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Waldo\OpenIdConnect\ProviderBundle\Events\OICProviderEvent;
use Waldo\OpenIdConnect\ProviderBundle\Events\AccountEvent;

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
        /* @var $userRegistration \Waldo\OpenIdConnect\ProviderBundle\Services\RegistrationUserService */
        $userRegistration = $this->get("waldo_oic_p.registration.user");

        $user = $userRegistration->getBlankUser();

        $form = $this->createForm($userRegistration->getFormType(), $user);

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
    public function accountValidationAction(Request $request, $token)
    {
        $isValid = $this->get("waldo_oic_p.registration.user")->handleValidationToken($token);
        
        return array("isValid" => $isValid);
    }

}
