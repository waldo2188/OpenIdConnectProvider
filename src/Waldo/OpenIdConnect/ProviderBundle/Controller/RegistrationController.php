<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/registration", name="oicp_registration")
 */
class RegistrationController extends Controller
{

    /**
     * @Route("new-account/", name="oicp_registration_new_account")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function registerAction(Request $request)
    {
        /* @var $userRegistration \Waldo\OpenIdConnect\ProviderBundle\Services\RegistrationUserService */
        $userRegistration = $this->get("waldo_oic_p.registration.user");
        
        $form = $this->createForm($userRegistration->getFormType(), $userRegistration->getBlankUser());
        
        $form->handleRequest($request);
        
        if ('POST' === $request->getMethod()) {
            if($form->isValid()) {
                
            }
        }
        
        return array(
            'form' => $form->createView()
        );     
    }

}
