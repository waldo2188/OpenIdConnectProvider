<?php

namespace Waldo\OpenIdConnect\ProviderBundle\AuthenticationFlows;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication;
use Waldo\OpenIdConnect\ProviderBundle\Utils\CodeHelper;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\HttpUtils;
use Doctrine\ORM\EntityManager;

/**
 * AuthenticationCodeFlow
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationCodeFlow
{

    /**
     * @var SecurityContext 
     */
    protected $securityContext;

    /**
     * @var Session 
     */
    protected $session;

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var HttpUtils 
     */
    protected $httpUtils;

    public function __construct(SecurityContext $securityContext,
            Session $session, EntityManager $em,
            HttpUtils $httpUtils)
    {
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->em = $em;
        $this->httpUtils = $httpUtils;
    }

    /**
     * 
     * @param Authentication $authentication
     * @return RedirectResponse
     */
    public function handle(Authentication $authentication)
    {
        
        $result = $this->checkUser($authentication);

        if($result instanceof RedirectResponse){
            return $result;
        }

        return $this->handleAccept($authentication);
        
    }

    /**
     * Get back the enduser with an access denied error
     * @param Authentication $authentication
     * @return RedirectResponse
     */
    public function handleCancel(Authentication $authentication)
    {
        $parameters = array('error' => 'access_denied', 'error_description' => 'scope denied by enduser');
                
        return $this->prepareRedirectResponse($authentication, $parameters);
    }
    
    /**
     * Get back the enduser with a code parameter
     * @param Authentication $authentication
     * @return RedirectResponse
     */
    public function handleAccept(Authentication $authentication)
    {
        $code = CodeHelper::generateUniqueCode(
                $this->em->getRepository("WaldoOpenIdConnectProviderBundle:Token"),
                'codeToken'
                );
        
        $this->em->getRepository("WaldoOpenIdConnectProviderBundle:Token")
                ->setCode(
                        $this->securityContext->getToken()->getUser()->getId(),
                        $authentication->getClientId(),
                        $code,
                        $authentication->getScope(),
                        $authentication->getNonce(),
                        $authentication->getRedirectUri()
                        );
        
        $this->session->remove('oicp.authentication.flow.code');
        $this->session->remove('oicp.authentication.flow.manager');
        
        $parameters = array('code' => $code);

        return $this->prepareRedirectResponse($authentication, $parameters);
    }


    public function getAuthentication()
    {
        return $this->session->get('oicp.authentication.flow.code');
    }
    
    public function getName()
    {
        return 'waldo_oic_p.authflow.code';
    }
    
    /**
     * Prepare a RedirectResponse
     * 
     * @param Authentication $authentication
     * @param array $parameters
     * @return RedirectResponse
     */
    protected function prepareRedirectResponse(Authentication $authentication, array $parameters)
    {
        if($authentication->getState() != null) {
            $parameters['state'] = $authentication->getState();
        }
        if($authentication->getNonce() != null) {
            $parameters['nonce'] = $authentication->getNonce();
        }
        
        $uri = Request::create($authentication->getRedirectUri(), 'GET', $parameters)->getUri();
        
        return new RedirectResponse($uri);
    }
    
    /**
     * 
     * @param \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication $authentication
     * @return boolean
     * @throws AuthenticationRequestException
     */
    protected function checkUser(Authentication $authentication)
    {

        $needAuthent = false;
        
        if($this->securityContext->getToken() == null)
        {
            $needAuthent = true;
        }
        
        // Check if user is well authenticated
        if(!$this->securityContext->getToken()->isAuthenticated() 
                || !$this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $needAuthent = true;
        }
        

        // Check max_age
        if($this->securityContext->getToken()->hasAttribute("ioc.token.issuedAt")) {
            
            $diff = $this->securityContext->getToken()
                    ->getAttribute("ioc.token.issuedAt")
                    ->diff(new \DateTime('now'));
            
            if($diff->s > $authentication->getMaxAge()) {
                $needAuthent = true;
            }
        }
        
        
        if($authentication->getPrompt() === Authentication::PROMPT_NONE && $needAuthent === true) {
            throw new AuthenticationRequestException('enduser need to login', 'login_required');
        }
        
        if($authentication->getPrompt() === Authentication::PROMPT_LOGIN) {
            $needAuthent = true;
        }
                
        if($authentication->getPrompt() === Authentication::PROMPT_SELECT_ACCOUNT) {
            throw new AuthenticationRequestException('enduser need to select an account', 'account_selection_required');
        }
        
        if($authentication->getPrompt() === Authentication::PROMPT_CONSENT && $needAuthent === false) {
            return $this->httpUtils->createRedirectResponse(new Request(), "oicp_authentication_scope");
        }
        
        if($needAuthent === true) {
            $this->securityContext->setToken(null);
            $this->session->set('oicp.authentication.flow.code', $authentication);
            $this->session->set('oicp.authentication.flow.manager', $this->getName());
            return $this->httpUtils->createRedirectResponse(new Request(), "login");            
        }
        
        return true;
    }
   
}
