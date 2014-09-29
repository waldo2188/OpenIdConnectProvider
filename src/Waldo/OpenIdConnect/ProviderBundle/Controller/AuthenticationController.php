<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Controller;

use Waldo\OpenIdConnect\ProviderBundle\Form\Type\ScopeApprovalType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * @Route("/authentication", name="oicp_authentication")
 */
class AuthenticationController extends Controller
{
    /** 
     * @Route("/login/{clientId}", name="login", defaults={"clientId": null})
     * @Template()
     */
    public function loginAction(Request $request, $clientId = null)
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
  
        $client = null;        
        $authFlowManager = 'oicp.authentication.flow.manager.' . $clientId;
        
        if($request->getSession()->has($authFlowManager)) {
            $clientId = $authenticationFlowManager = $this->get(
                    $request->getSession()->get($authFlowManager)
            )->getAuthentication($clientId)
                    ->getClientId();

            $client = $this->getDoctrine()->getRepository("WaldoOpenIdConnectModelBundle:Client")
                    ->findOneByClientId($clientId);
        }

        return array(
            // last username entered by the user
            'client' => $client,
            'user' => $this->getTokenUser(),
            'last_username' => $lastUsername,
            'error' => $error,
        );
    }
    
    private function getTokenUser()
    {
        if ($this->get('security.context')->getToken() !== null) {
            if ($this->get('security.context')->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)
                || $this->get('security.context')->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_REMEMBERED)) {
                
                $user = $this->get('security.context')->getToken()->getUser();
                
                $this->get('session')->set('oic.login.auth.user', $user->getId());
                
                return $user;
            }
        }
        
        if($this->get('session')->has('oic.login.auth.user')) {
                        
            return $this->getDoctrine()->getManager()->getRepository("WaldoOpenIdConnectModelBundle:Account")
                    ->findOneById($this->get('session')->get('oic.login.auth.user'));
        }
        
        return null;
    }
    
    /**
     * @Route("/scope/{clientId}", name="oicp_authentication_scope")
     * @Template()
     */
    public function scopeApprovalAction(Request $request, $clientId)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if(!$request->getSession()->has('oicp.authentication.flow.manager.' . $clientId)) {
            throw new NotFoundHttpException();
        }
        
        $authenticationFlowManager = $this->get(
                $request->getSession()->get('oicp.authentication.flow.manager.' . $clientId)
                );
        
        $authentication = $authenticationFlowManager->getAuthentication($clientId);
        
        $client = $this->getDoctrine()->getManager()->getRepository("WaldoOpenIdConnectModelBundle:Client")
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
