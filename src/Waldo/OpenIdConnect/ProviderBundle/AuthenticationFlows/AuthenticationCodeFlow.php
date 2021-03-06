<?php

namespace Waldo\OpenIdConnect\ProviderBundle\AuthenticationFlows;

use Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication;
use Waldo\OpenIdConnect\ProviderBundle\Utils\TokenCodeGenerator;
use Waldo\OpenIdConnect\ProviderBundle\Exception\AuthenticationRequestException;
use Waldo\OpenIdConnect\ProviderBundle\Services\ScopeUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
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

    /**
     * @var ScopeUtils 
     */
    protected $scopeUtils;

    public function __construct(SecurityContextInterface $securityContext,
            Session $session, EntityManager $em,
            HttpUtils $httpUtils, ScopeUtils $scopeUtils)
    {
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->em = $em;
        $this->httpUtils = $httpUtils;
        $this->scopeUtils = $scopeUtils;
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
        $code = TokenCodeGenerator::generateUniqueCode(
                $this->em->getRepository("WaldoOpenIdConnectModelBundle:Token"),
                'codeToken'
                );
        
        $this->em->getRepository("WaldoOpenIdConnectModelBundle:Token")
                ->setCode(
                        $this->securityContext->getToken()->getUser()->getId(),
                        $authentication->getClientId(),
                        $code,
                        $authentication->getScope(),
                        $authentication->getNonce(),
                        $authentication->getRedirectUri()
                        );
        
        $this->session->remove('oicp.authentication.flow.code.' . $authentication->getClientId());
        $this->session->remove('oicp.authentication.flow.manager.' . $authentication->getClientId());
        
        $parameters = array('code' => $code);

        return $this->prepareRedirectResponse($authentication, $parameters);
    }


    /**
     * 
     * @param string $clientId
     * @return Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication
     */
    public function getAuthentication($clientId)
    {
        if($this->session->has('oicp.authentication.flow.code.' . $clientId)) {
            return $this->session->get('oicp.authentication.flow.code.' . $clientId);
        }
        return null;
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
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication $authentication
     * @return boolean
     * @throws AuthenticationRequestException
     */
    protected function checkUser(Authentication $authentication)
    {
        $needAuthent = false;
        
        if($this->securityContext->getToken() == null)
        {
            return $this->needAuthRequest($authentication);
        }
                
        // Check if user is well authenticated
        if(!$this->securityContext->getToken()->isAuthenticated() 
                || !$this->securityContext->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
            return $this->needAuthRequest($authentication);
        }
        

        // Check max_age
        if($this->securityContext->getToken()->hasAttribute("ioc.token.issuedAt")) {

            /* @var $issuedAt \DateTime */
            $issuedAt = $this->securityContext->getToken()
                    ->getAttribute("ioc.token.issuedAt");

            $issuedAt->modify( sprintf("+%d seconds", $authentication->getMaxAge()));
            
            if(new \DateTime('now') > $issuedAt) {
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
        
        if($this->scopeUtils->needToValideScope(
                $this->securityContext->getToken()->getUser(),
                $authentication
                )) {
            return $this->needScopeRequest($authentication);
        }
        
        if($needAuthent === true) {
            return $this->needAuthRequest($authentication);
        }

        return true;
    }

    /**
     * Create a redirect response to the login form
     * 
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication $authentication
     * @return RedirectResponse
     */
    private function needAuthRequest(Authentication $authentication)
    {
        $this->securityContext->setToken(null);
        
        $this->session->set('oicp.authentication.flow.code.' . $authentication->getClientId(), $authentication);
        $this->session->set('oicp.authentication.flow.manager.' . $authentication->getClientId(), $this->getName());
        
        $request = new Request(array(), array(), array('clientId' => $authentication->getClientId()));
        return $this->httpUtils->createRedirectResponse($request, "login");
    }

    /**
     * Create a redirect response to the scope form
     * 
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication $authentication
     * @return RedirectResponse
     */
    private function needScopeRequest(Authentication $authentication)
    {
        $this->session->set('oicp.authentication.flow.code.' . $authentication->getClientId(), $authentication);
        $this->session->set('oicp.authentication.flow.manager.' . $authentication->getClientId(), $this->getName());
        
        $request = new Request(array(), array(), array('clientId' => $authentication->getClientId()));
        return $this->httpUtils->createRedirectResponse($request, "oicp_authentication_scope");
    }

}
