<?php

namespace Waldo\OpenIdConnect\ProviderBundle\AuthenticationFlows;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
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

    public function __construct(SecurityContext $securityContext, Session $session, EntityManager $em)
    {
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->em = $em;
    }

    public function handle(Authentication $authentication)
    {
        
        $this->checkUser($authentication);
        
        echo "<pre>AuthenticationCodeFlow handle:";
        var_dump($authentication);
        echo "</pre>";



        exit;
        
    }

    protected function checkUser(Authentication $authentication)
    {

        $needAuthent = false;
        
        if($this->securityContext->getToken() == null)
        {
            $needAuthent = true;
        // TODO need to authent enduser    
        }
        
        if(!$this->securityContext->getToken()->isAuthenticated()) {
            $needAuthent = true;
        // TODO need to authent enduser    
        }
       
        // Check max_age
        if($this->securityContext->getToken()->hasAttribute("ioc.token.issuedAt")) {
            
            $diff = $this->securityContext->getToken()
                    ->getAttribute("ioc.token.issuedAt")
                    ->diff(new \DateTime('now'));
            
            if($diff->s > $authentication->getMaxAge()) {
                $needAuthent = true;
                // TODO need to authent enduser
            }
        }
        
        if($authentication->getPrompt() === Authentication::PROMPT_NONE && $needAuthent === true) {
            //TODO throw error code will typically be login_required, interaction_required, or another code defined 
        }
        
        if($authentication->getPrompt() === Authentication::PROMPT_LOGIN) {
            $needAuthent = true;
        }
        
        if($authentication->getPrompt() === Authentication::PROMPT_CONSENT && $needAuthent === false) {
            //TODO redirect to the scope page
        }
        
        if($authentication->getPrompt() === Authentication::PROMPT_SELECT_ACCOUNT) {
            //TODO throw return an error, typically account_selection_required. 
        }
        
        if($needAuthent === true) {
            $this->securityContext->setToken(null);
        }
        
        
    }
    
}
