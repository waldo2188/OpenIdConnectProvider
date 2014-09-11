<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication;
use Waldo\OpenIdConnect\ProviderBundle\Constraints\AuthenticationRequestValidator;
use Waldo\OpenIdConnect\ProviderBundle\Exception\AuthenticationRequestException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * AuthorizationEndpoint
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthorizationEndpoint
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
     * @var AuthenticationRequestValidator 
     */
    protected $authenticationRequestValidator;

    /**
     * 
     * @param \Symfony\Component\Security\Core\SecurityContext $securityContext
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(SecurityContext $securityContext, Session $session,
            AuthenticationRequestValidator $authenticationRequestValidator)
    {
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->authenticationRequestValidator = $authenticationRequestValidator;
    }

    public function handleRequest(Request $request)
    {
        $authentication = $this->extractAutenticationFromRequest($request);
        
        try{
            $this->authenticationRequestValidator->validate($authentication);
        } catch (AuthenticationRequestException $ex) {
            
            if($authentication->getRedirectUri() != null) {             
                $parameters = array('error' => $ex->getError(), 'error_description' => $ex->getMessage());
                $uri = Request::create($authentication->getRedirectUri(), 'GET', $parameters)->getUri();
               
                return new RedirectResponse($uri);
            }
            
            throw $ex;
        }
        
        
    }
    
    /**
     * Extract Authentication parameters set in the query
     * 
     * @example /authorize?response_type=code&scope=openid%20profile%20email
     * &client_id=s6BhdRkqt3&state=af0ifjsldkj
     * &redirect_uri=https%3A%2F%2Fclient.example.org%2Fcb&somethingelse=nop
     * 
     * Will extract response_type, scope, client_id, state, redirect_uri,
     * but not somethingelse
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    protected function extractAutenticationFromRequest(Request $request)
    {
        $fieldMap = array(
            'scope' => 'scope',
            'response_type' => 'responseRype',
            'client_id' => 'clientId',
            'redirect_uri' => 'redirectUri',
            'state' => 'state',
            'response_mode' => 'responseMode',
            'nonce' => 'nonce',
            'display' => 'display',
            'prompt' => 'prompt',
            'max_age' => 'maxAge',
            'ui_locales' => 'uiLocales',
            'id_token_hint' => 'idTokenHint',
            'login_hint' => 'loginHint'
        );
        
        $authentication = new Authentication();
        
        foreach($fieldMap as $parameter => $property) {
            if($request->query->has($parameter)) {
                $methode = 'set' . ucfirst($property);
                $authentication->$methode( $request->query->get($parameter) );
            }
        }
        
        return $authentication;
    }
    
}
