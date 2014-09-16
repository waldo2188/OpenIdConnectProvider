<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Entity\Request;

/**
 * 
 * @see http://openid.net/specs/openid-connect-core-1_0.html#AuthRequest
 */
class Authentication implements \Serializable
{

    const AUTHORISATION_CODE_FLOW = "Authorization Code Flow";    
    const IMPLICIT_FLOW = "Implicit Flow";
    const HYBRID_FLOW = "Hybrid Flow";
    
    const PROMPT_NONE = "none";
    const PROMPT_LOGIN = "login";
    const PROMPT_CONSENT = "consent";
    const PROMPT_SELECT_ACCOUNT = "select_account";


    /**
     * REQUIRED. OpenID Connect requests MUST contain the openid scope value.
     * If the openid scope value is not present, the behavior is entirely unspecified.
     * Other scope values MAY be present. Scope values used that are not 
     * understood by an implementation SHOULD be ignored.
     * 
     * @var array 
     */
    protected $scope;

    /**
     * OAuth 2.0 Response Type value that determines the authorization processing
     * flow to be used, including what parameters are returned from the endpoints
     * used. When using the Authorization Code Flow, this value is code. 
     * 
     * @var string
     */
    protected $responseType;

    /**
     * OAuth 2.0 Client Identifier valid at the Authorization Server. 
     * 
     * @var string
     */
    protected $clientId;

    /**
     * REQUIRED. Redirection URI to which the response will be sent. 
     * This URI MUST exactly match one of the Redirection URI values for the 
     * Client pre-registered at the OpenID Provider, with the matching performed 
     * as described in Section 6.2.1 of [RFC3986] (Simple String Comparison). 
     * When using this flow, the Redirection URI SHOULD use the https scheme; 
     * however, it MAY use the http scheme, provided that the Client Type is 
     * confidential, as defined in Section 2.1 of OAuth 2.0, and provided the OP 
     * allows the use of http Redirection URIs in this case. 
     * The Redirection URI MAY use an alternate scheme, such as one that is 
     * intended to identify a callback into a native application. 
     * 
     * @var string 
     */
    protected $redirectUri;

    /**
     * Opaque value used to maintain state between the request and the callback.
     * Typically, Cross-Site Request Forgery (CSRF, XSRF) mitigation is done by 
     * cryptographically binding the value of this parameter with a browser cookie. 
     * @var string
     */
    protected $state;

    /**
     *  Informs the Authorization Server of the mechanism to be used for 
     * returning parameters from the Authorization Endpoint. This use of this 
     * parameter is NOT RECOMMENDED when the Response Mode that would be 
     * requested is the default mode specified for the Response Type. 
     * 
     * @see http://openid.net/specs/oauth-v2-multiple-response-types-1_0.html
     * @var string
     */
    protected $responseMode;

    /**
     * String value used to associate a Client session with an ID Token, and to
     * mitigate replay attacks. The value is passed through unmodified from the 
     * Authorization Request to the ID Token. Sufficient entropy MUST be present
     * in the nonce values used to prevent attackers from guessing values. 
     * @var string 
     */
    protected $nonce;

    /**
     * ASCII string value that specifies how the Authorization Server displays
     * the authentication and consent user interface pages to the End-User.
     * The defined values are:
     *  - page
     *      The Authorization Server SHOULD display the authentication and consent 
     *      UI consistent with a full User Agent page view. If the display parameter
     *      is not specified, this is the default display mode. 
     *  - popup
     *      The Authorization Server SHOULD display the authentication and consent
     *      UI consistent with a popup User Agent window. The popup User Agent 
     *      window should be of an appropriate size for a login-focused dialog 
     *      and should not obscure the entire window that it is popping up over. 
     *  - touch
     *      The Authorization Server SHOULD display the authentication and 
     *      consent UI consistent with a device that leverages a touch interface. 
     *  - wap
     *      The Authorization Server SHOULD display the authentication and consent 
     *      UI consistent with a "feature phone" type display. 
     * 
     * @var string
     */
    protected $display;

    /**
     * 	Space delimited, case sensitive list of ASCII string values that specifies
     * whether the Authorization Server prompts the End-User for reauthentication
     * and consent.
     * The defined values are:
     *  - none
     *      The Authorization Server MUST NOT display any authentication or 
     *      consent user interface pages. An error is returned if an End-User is
     *      not already authenticated or the Client does not have pre-configured
     *      consent for the requested Claims or does not fulfill other conditions
     *      for processing the request. The error code will typically be 
     *      login_required, interaction_required, or another code defined in 
     *      Section 3.1.2.6. This can be used as a method to check for 
     *      existing authentication and/or consent. 
     *  - login
     *      The Authorization Server SHOULD prompt the End-User for reauthentication.
     *      If it cannot reauthenticate the End-User, it MUST return an error,
     *      typically login_required. 
     *  - consent
     *      The Authorization Server SHOULD prompt the End-User for consent 
     *      before returning information to the Client. If it cannot obtain 
     *      consent, it MUST return an error, typically consent_required. 
     *  - select_account
     *      The Authorization Server SHOULD prompt the End-User to select a user
     *      account. This enables an End-User who has multiple accounts at the 
     *      Authorization Server to select amongst the multiple accounts that 
     *      they might have current sessions for. If it cannot obtain an account
     *      selection choice made by the End-User, it MUST return an error,
     *      typically account_selection_required. 
     * 
     * @var string 
     */
    protected $prompt;

    /**
     * Maximum Authentication Age. Specifies the allowable elapsed time in seconds
     * since the last time the End-User was actively authenticated by the OP. 
     * If the elapsed time is greater than this value, the OP MUST attempt to 
     * actively re-authenticate the End-User. (The max_age request parameter 
     * corresponds to the OpenID 2.0 PAPE [OpenID.PAPE] max_auth_age request 
     * parameter.) When max_age is used, the ID Token returned MUST include an
     * auth_time Claim Value. 
     * 
     * @var integer
     */
    protected $maxAge;

    /**
     * End-User's preferred languages and scripts for the user interface, 
     * represented as a space-separated list of BCP47 [RFC5646] language tag 
     * values, ordered by preference. For instance, the value "fr-CA fr en" 
     * represents a preference for French as spoken in Canada, then French 
     * (without a region designation), followed by English (without a region 
     * designation). An error SHOULD NOT result if some or all of the requested
     * locales are not supported by the OpenID Provider. 
     * 
     * @var string
     */
    protected $uiLocales;

    /**
     * Maximum Authentication Age. Specifies the allowable elapsed time in seconds
     * since the last time the End-User was actively authenticated by the OP. 
     * If the elapsed time is greater than this value, the OP MUST attempt to 
     * actively re-authenticate the End-User. (The max_age request parameter 
     * corresponds to the OpenID 2.0 PAPE [OpenID.PAPE] max_auth_age request parameter.) 
     * When max_age is used, the ID Token returned MUST include an auth_time 
     * Claim Value. 
     *
     * @var string 
     */
    protected $idTokenHint;

    /**
     * Hint to the Authorization Server about the login identifier the End-User
     * might use to log in (if necessary). This hint can be used by an RP if it 
     * first asks the End-User for their e-mail address (or other identifier) 
     * and then wants to pass that value as a hint to the discovered authorization
     * service. It is RECOMMENDED that the hint value match the value used for 
     * discovery. This value MAY also be a phone number in the format specified 
     * for the phone_number Claim. The use of this parameter is left to the OP's 
     * discretion. 
     * 
     * @var string 
     */
    protected $loginHint;

    protected $knowResponseTypes = array('code', 'token', 'id_token');


    /**
     * 
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * 
     * @return string
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Return true if valid, string with unknown response type otherwise
     * @return boolean | string
     */
    public function isResponseTypeValid()
    {
        $valid = null;
        foreach ($this->responseType as $responsType) {
            if(!in_array($responsType, $this->knowResponseTypes)) {
                $valid .= $responsType . " ";
            }
        }
        
        return $valid === null ? true : $valid;
    }

    /**
     * 
     * @return string
     */ public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * 
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * 
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * 
     * @return string
     */
    public function getResponseMode()
    {
        return $this->responseMode;
    }

    /**
     * 
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * 
     * @return string
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * 
     * @return string
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * 
     * @return integer
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    /**
     * 
     * @return string
     */
    public function getUiLocales()
    {
        return $this->uiLocales;
    }

    /**
     * 
     * @return string
     */
    public function getIdTokenHint()
    {
        return $this->idTokenHint;
    }

    /**
     * 
     * @return string
     */
    public function getLoginHint()
    {
        return $this->loginHint;
    }

    /**
     * 
     * @param string $scope
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setScope($scope)
    {
        if(is_string($scope)) {
            $this->scope = explode(' ', $scope);
        }elseif(is_array($scope)) {
            $this->scope = $scope;
        }
        
        return $this;
    }

    /**
     * 
     * @param array $responseType
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setResponseType($responseType)
    {
        $this->responseType = explode(' ', $responseType);
        return $this;
    }

    /**
     * 
     * @param string $clientId
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * 
     * @param string $redirectUri
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    /**
     * 
     * @param string $state
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * 
     * @param string $responseMode
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setResponseMode($responseMode)
    {
        $this->responseMode = $responseMode;
        return $this;
    }

    /**
     * 
     * @param string $nonce
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * 
     * @param string $display
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setDisplay($display)
    {
        $this->display = $display;
        return $this;
    }

    /**
     * 
     * @param string $prompt
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;
        return $this;
    }

    /**
     * 
     * @param integer $maxAge
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setMaxAge($maxAge)
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    /**
     * 
     * @param string $uiLocales
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setUiLocales($uiLocales)
    {
        $this->uiLocales = $uiLocales;
        return $this;
    }

    /**
     * 
     * @param string $idTokenHint
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setIdTokenHint($idTokenHint)
    {
        $this->idTokenHint = $idTokenHint;
        return $this;
    }

    /**
     * 
     * @param string $loginHint
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication
     */
    public function setLoginHint($loginHint)
    {
        $this->loginHint = $loginHint;
        return $this;
    }
    
    /**
     * Get the flow type: Authorization Code Flow, Implicit Flow, Hybrid Flow
     * 
     * @see http://openid.net/specs/openid-connect-core-1_0.html#Authentication
     * @return null | string
     */
    public function getFlowType()
    {       
        if(count($this->responseType) == 1 && in_array('code', $this->responseType)) {
            return self::AUTHORISATION_CODE_FLOW;
        }
        
        if(count($this->responseType) == 1 && in_array('id_token', $this->responseType)) {
            return self::IMPLICIT_FLOW;
        }
        
        if(count($this->responseType) == 2
                && in_array('id_token', $this->responseType)
                && in_array('token', $this->responseType)) {
            return self::IMPLICIT_FLOW;
        }
        
        if(count($this->responseType) == 2
                && in_array('code', $this->responseType)
                && (in_array('id_token', $this->responseType) 
                        || in_array('token', $this->responseType))) {
            return self::HYBRID_FLOW;
        }
        
        if(count($this->responseType) == 3
                && in_array('code', $this->responseType)
                && in_array('id_token', $this->responseType) 
                && in_array('token', $this->responseType)) {
            return self::HYBRID_FLOW;
        }
        
        return null;
    }
    
    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->clientId,
            $this->display,
            $this->idTokenHint,
            $this->loginHint,
            $this->maxAge,
            $this->nonce,
            $this->prompt,
            $this->redirectUri,
            $this->responseMode,
            $this->responseType,
            $this->scope,
            $this->state,
            $this->uiLocales
        ));
    }
    
    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list (
                $this->clientId,
            $this->display,
            $this->idTokenHint,
            $this->loginHint,
            $this->maxAge,
            $this->nonce,
            $this->prompt,
            $this->redirectUri,
            $this->responseMode,
            $this->responseType,
            $this->scope,
            $this->state,
            $this->uiLocales
                ) = unserialize($serialized);
    }

}
