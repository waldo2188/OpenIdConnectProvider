<?php

namespace Waldo\OpenIdConnect\ProviderBundle\AuthenticationFlows;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication;
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

    public function handle(Authentication $authentication)
    {
        
        $result = $this->checkUser($authentication);
        
        if($result instanceof RedirectResponse){
            return $result;
        }

        //TODO generate code and redirect to OIC RP
        
    }

    protected function checkUser(Authentication $authentication)
    {

        $needAuthent = false;
        
        if($this->securityContext->getToken() == null)
        {
            $needAuthent = true;
        }
        
        // Check if user is well authenticated
        if(!$this->securityContext->getToken()->isAuthenticated()) {
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
            return $this->httpUtils->createRedirectResponse(new Request(), "login");            
        }
        
        return true;
    }
    
}
