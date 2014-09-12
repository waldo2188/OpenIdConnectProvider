<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Constraints;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication;
use Waldo\OpenIdConnect\ProviderBundle\Exception\AuthenticationRequestException;
use Doctrine\ORM\EntityManager;

/**
 * AuthenticationRequest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationRequestValidator
{
    
    /**
     * @var EntityManager 
     */
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    
    public function validate(Authentication $authentication)
    {
        // Check for required parameters
        if($authentication->getScope() == null || count($authentication->getScope()) == 0) {
            throw new AuthenticationRequestException('scope is missing', 'invalid_request');
        }
        
        if($authentication->getResponseType() == null || count($authentication->getResponseType()) == 0) {
            throw new AuthenticationRequestException('response_type is missing', 'invalid_request');
        }
        
        if($authentication->getClientId() == null || $authentication->getClientId() == "") {
            throw new AuthenticationRequestException('client_id is missing', 'invalid_request');
        }
        
        if($authentication->getRedirectUri() == null || $authentication->getRedirectUri() == "") {
            throw new AuthenticationRequestException('redirect_uri is missing', 'invalid_request');
        }
        
        // check for openid scope
        if(!in_array('openid', $authentication->getScope())) {
            throw new AuthenticationRequestException('openid scope is missing', 'invalid_scope');
        }
        
        /* @var $client \Waldo\OpenIdConnect\ProviderBundle\Entity\Client */
        $client = $this->em->getRepository("WaldoOpenIdConnectProviderBundle:Client")
                ->findOneByClientId($authentication->getClientId());
        
        // Check if the client exist
        if($client === null) {
            throw new AuthenticationRequestException('client_id not found', 'unauthorized_client');
        }

        // Check if redirect_uri is a valide one
        if(in_array($authentication->getRedirectUri(), $client->getRedirectUris())) {
            throw new AuthenticationRequestException('no matching request_uri', 'invalid_request');
        }
        
        if(!$authentication->isResponseTypeValid()) {
            throw new AuthenticationRequestException(
                    "Unknown response_type " . $authentication->getResponseType(),
                    'invalid_response_type');
        }
        
        if($authentication->getFlowType() === null) {
            throw new AuthenticationRequestException("unknow authentication flow", "invalid_request");
        }
        
        // if no nonce and response_type contain token or id_token
        if( ($authentication->getNonce() == null || $authentication->getNonce() == "") 
                && (in_array('token', $authentication->getResponseType()) 
                    || in_array('id_token', $authentication->getResponseType())) ) {
            throw new AuthenticationRequestException('nonce is missing', 'invalid_request');
        }
            
        return true;
    }
    
}
