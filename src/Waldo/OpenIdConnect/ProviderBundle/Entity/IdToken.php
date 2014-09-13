<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Entity;

/**
 * @see http://openid.net/specs/openid-connect-core-1_0.html#IDToken
 * 
 */
class IdToken
{  
    /**
     * REQUIRED. Issuer Identifier for the Issuer of the response. 
     * The iss value is a case sensitive URL using the https scheme 
     * that contains scheme, host, and optionally, port number and path 
     * components and no query or fragment components. 
     * 
     * @var string
     */
    protected $iss;

    /**
     * REQUIRED. Subject Identifier. 
     * A locally unique and never reassigned identifier within the Issuer 
     * for the End-User, which is intended to be consumed by the Client, 
     * e.g., 24400320 or AItOawmwtWwcT0k51BayewNvutrJUqsvl6qs7A4. 
     * It MUST NOT exceed 255 ASCII characters in length. 
     * The sub value is a case sensitive string. 
     * 
     * @var string
     */
    protected $sub;

    /**
     * REQUIRED. Audience(s) that this ID Token is intended for.
     * It MUST contain the OAuth 2.0 client_id of the Relying Party as 
     * an audience value. It MAY also contain identifiers for other audiences. 
     * In the general case, the aud value is an array of case sensitive strings.
     * In the common special case when there is one audience,
     * the aud value MAY be a single case sensitive string.
     * 
     * @var string
     */
    protected $aud;

    /**
     * REQUIRED. Expiration time on or after which the ID Token MUST NOT be 
     * accepted for processing. The processing of this parameter requires that 
     * the current date/time MUST be before the expiration date/time listed in 
     * the value. Implementers MAY provide for some small leeway, usually no
     * more than a few minutes, to account for clock skew. Its value is a 
     * JSON number representing the number of seconds from 1970-01-01T0:0:0Z 
     * as measured in UTC until the date/time. See RFC 3339 [RFC3339] for 
     * details regarding date/times in general and UTC in particular.
     * 
     * @var string
     */
    protected $exp;

    /**
     * REQUIRED. Time at which the JWT was issued. 
     * Its value is a JSON number representing the number of seconds 
     * from 1970-01-01T0:0:0Z as measured in UTC until the date/time. 
     * 
     * @var string
     */
    protected $iat;

    /**
     * Time when the End-User authentication occurred.
     * Its value is a JSON number representing the number of seconds 
     * from 1970-01-01T0:0:0Z as measured in UTC until the date/time. 
     * When a max_age request is made or when auth_time is requested as an 
     * Essential Claim, then this Claim is REQUIRED; otherwise, its inclusion 
     * is OPTIONAL. (The auth_time Claim semantically corresponds to the OpenID
     * 2.0 PAPE [OpenID.PAPE] auth_time response parameter.) 
     * 
     * @var string
     */
    protected $auth_time;

    /**
     * String value used to associate a Client session with an ID Token, 
     * and to mitigate replay attacks. The value is passed through unmodified 
     * from the Authentication Request to the ID Token. 
     * If present in the ID Token, Clients MUST verify that the nonce Claim 
     * Value is equal to the value of the nonce parameter sent in the 
     * Authentication Request. If present in the Authentication Request, 
     * Authorization Servers MUST include a nonce Claim in the ID Token 
     * with the Claim Value being the nonce value sent in the Authentication 
     * Request. Authorization Servers SHOULD perform no other processing on 
     * nonce values used. The nonce value is a case sensitive string. 
     * 
     * @var string
     */
    protected $nonce;

    /**
     * OPTIONAL. Authorized party - the party to which the ID Token was issued.
     * If present, it MUST contain the OAuth 2.0 Client ID of this party.
     * This Claim is only needed when the ID Token has a single audience value
     * and that audience is different than the authorized party. 
     * It MAY be included even when the authorized party is the same as 
     * the sole audience. The azp value is a case sensitive string containing a 
     * StringOrURI value.
     * 
     * @var string
     */
    protected $azp;

    private $property = array('iss', 'sub', 'aud', 'exp', 'iat', 'auth_time', 'nonce', 'azp');
    
    public function getIss()
    {
        return $this->iss;
    }

    public function getSub()
    {
        return $this->sub;
    }

    public function getAud()
    {
        return $this->aud;
    }

    public function getExp()
    {
        return $this->exp;
    }

    public function getIat()
    {
        return $this->iat;
    }

    public function getAuth_time()
    {
        return $this->auth_time;
    }

    public function getNonce()
    {
        return $this->nonce;
    }

    public function getAzp()
    {
        return $this->azp;
    }

    public function setIss($iss)
    {
        $this->iss = $iss;
        return $this;
    }

    public function setSub($sub)
    {
        $this->sub = $sub;
        return $this;
    }

    public function setAud($aud)
    {
        $this->aud = $aud;
        return $this;
    }

    public function setExp($exp)
    {
        $this->exp = $exp;
        return $this;
    }

    public function setIat($iat)
    {
        $this->iat = $iat;
        return $this;
    }

    public function setAuth_time($auth_time)
    {
        $this->auth_time = $auth_time;
        return $this;
    }

    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
        return $this;
    }

    public function setAzp($azp)
    {
        $this->azp = $azp;
        return $this;
    }

    public function __toArray()
    {
        $out = array();
        
        foreach($this->property as $property) {
            if($this->$property != null) {
                $out[$property] = $this->$property;
            }
        }
        
        return $out;
    }

}
