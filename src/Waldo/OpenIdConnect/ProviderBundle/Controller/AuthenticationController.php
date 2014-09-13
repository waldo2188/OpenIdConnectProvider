<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Controller;

use Waldo\OpenIdConnect\ProviderBundle\Form\Type\ScopeApprovalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/authentication", name="oicp_authentication")
 */
class AuthenticationController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return array(
            // last username entered by the user
            'last_username' => $lastUsername,
            'error' => $error,
        );
    }
    
    /**
     * @Route("/scope", name="oicp_authentication_scope")
     * @Template()
     */
    public function scopeApprovalAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $authenticationFlowManager = $this->get(
                $request->getSession()->get('oicp.authentication.flow.manager')
                );
        
        $authentication = $authenticationFlowManager->getAuthentication();
        
        $client = $this->getDoctrine()->getManager()->getRepository("WaldoOpenIdConnectProviderBundle:Client")
                ->findOneByClientId($authentication->getClientId());
        
        $userInfo = $this->get('waldo_oic_p.utils.scope')
                ->getUserinfoForScopes($user, $authentication);
        
        $form = $this->createForm(new ScopeApprovalType());

        if($request->isMethod("POST")) {
            
            $form->handleRequest($request);

            if($form->get('cancel')->isClicked()) {
                
                return $authenticationFlowManager->handleCancel($authentication);
                
            } elseif ($form->get('accept')->isClicked()){
                
                return $authenticationFlowManager->handleAccept($authentication);
                
            }                   
        }
        
        return array(
            'userinfos' => $userInfo,
            'client' => $client,
            'form' => $form->createView()
        );
    }

}
