<?php

namespace Waldo\OpenIdConnect\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="Waldo\OpenIdConnect\ModelBundle\EntityRepository\ClientRepository")
 * @ORM\Table(name="client")
 * @ORM\HasLifecycleCallbacks
 */
class Client implements UserInterface
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, unique=true)
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Token", mappedBy="client", cascade={"persist", "remove", "merge"})
     * 
     * @var ArrayCollection<Token> $tokenList
     */
    protected $tokenList;

    /**
     * @ORM\Column(name="client_id", type="string", length=255, nullable=true)
     * 
     * @var string $clientId
     */
    protected $clientId;

    /**
     * @ORM\Column(name="client_id_issued_at", type="datetime", nullable=true)
     * 
     * @var string $clientIdIssuedAt
     */
    protected $clientIdIssuedAt;

    /**
     * @ORM\Column(name="client_secret", type="string", length=255, nullable=true)
     * 
     * @var string $clientSecret
     */
    protected $clientSecret;

    /**
     * @ORM\Column(name="client_secret_expires_at", type="datetime", nullable=true)
     * 
     * @var \DateTime $clientSecretExpiresAt
     */
    protected $clientSecretExpiresAt;

    /**
     * @ORM\Column(name="client_name", type="string", length=255, nullable=true)
     * 
     * @var string $clientName
     */
    protected $clientName;

    /**
     * @ORM\Column(name="client_uri", type="string", length=255, nullable=true)
     * 
     * @var string $clientUri
     */
    protected $clientUri;

    /**
     * @ORM\Column(name="contacts", type="array", length=255, nullable=true)
     * 
     * @var string $contacts
     */
    protected $contacts;

    /**
     * @ORM\Column(name="application_type", type="string", length=255, nullable=true)
     * 
     * @see openid.net/specs/openid-connect-registration-1_0.html#ClientMetadata
     * @var string $applicationType (native, web)
     */
    protected $applicationType;

    /**
     * @ORM\Column(name="logo_uri", type="string", length=2048, nullable=true)
     * 
     * @var string $logoUri
     */
    protected $logoUri;

    /**
     * @ORM\Column(name="tos_uri", type="string", length=2048, nullable=true)
     * 
     * @var string $tosUri Term Of Use
     */
    protected $tosUri;

    /**
     * @ORM\Column(name="policy_uri", type="string", length=2048, nullable=true)
     * 
     * @var string $policyUri
     */
    protected $policyUri;

    /**
     * @ORM\Column(name="redirect_uris", type="array", nullable=true)
     * 
     * @var string $redirectUris
     */
    protected $redirectUris;

    /**
     * @ORM\Column(name="post_logout_redirect_uri", type="string", length=2048, nullable=true)
     * 
     * @var string $postLogoutRedirectUri
     */
    protected $postLogoutRedirectUri;

    /**
     * @ORM\Column(name="token_endpoint_auth_method", type="string", length=255, nullable=true)
     * 
     * @var string $tokenEndpointAuthMethod (client_secret_basic, client_secret_post)
     */
    protected $tokenEndpointAuthMethod = 'client_secret_basic';

    /**
     * @ORM\Column(name="token_endpoint_auth_signing_alg", type="string", length=255, nullable=true)
     * 
     * @var string $tokenEndpointAuthSigningAlg RS256, RS512, ...
     */
    protected $tokenEndpointAuthSigningAlg;

    /**
     * @ORM\Column(name="jwks_uri", type="string", length=2048, nullable=true)
     * 
     * @var string $jwksUri
     */
    protected $jwksUri;

    /**
     * @ORM\Column(name="jwk_encryption_uri", type="string", length=2048, nullable=true)
     * 
     * @var string $jwkEncryptionUri
     */
    protected $jwkEncryptionUri;

    /**
     * @ORM\Column(name="x509_uri", type="string", length=2048, nullable=true)
     * 
     * @var string $username
     */
    protected $x509Uri;

    /**
     * @ORM\Column(name="x509_encryption_uri", type="string", length=2048, nullable=true)
     * 
     * @var string $x509EncryptionUri
     */
    protected $x509EncryptionUri;

    /**
     * @ORM\Column(name="request_object_signing_alg", type="string", length=255, nullable=true)
     * 
     * @var string $requestObjectSigningAlg RS256, RS512, ...
     */
    protected $requestObjectSigningAlg;

    /**
     * @ORM\Column(name="userinfo_signed_response_alg", type="string", length=255, nullable=true)
     * 
     * @var string $userinfoSignedResponseAlg RS256, RS512, ...
     */
    protected $userinfoSignedResponseAlg;

    /**
     * @ORM\Column(name="userinfo_encrypted_response_alg", type="string", length=255, nullable=true)
     * 
     * @var string $userinfoEncryptedResponseAlg RS256, RS512, ...
     */
    protected $userinfoEncryptedResponseAlg;

    /**
     * @ORM\Column(name="userinfo_encrypted_response_enc", type="string", length=255, nullable=true)
     * 
     * @var string $userinfoEncryptedResponseEnc A128CBC-HS256
     */
    protected $userinfoEncryptedResponseEnc;

    /**
     * @ORM\Column(name="id_token_signed_response_alg", type="string", length=255, nullable=true)
     * 
     * @var string $idTokenSignedResponseAlg RS256, RS512, ...
     */
    protected $idTokenSignedResponseAlg;

    /**
     * @ORM\Column(name="id_token_encrypted_response_alg", type="string", length=255, nullable=true)
     * 
     * @var string $idTokenEncryptedResponseAlg RS256, RS512, ...
     */
    protected $idTokenEncryptedResponseAlg;

    /**
     * @ORM\Column(name="id_token_encrypted_response_enc", type="string", length=255, nullable=true)
     * 
     * @var string $idTokenEncryptedResponseEnc A128CBC-HS256
     */
    protected $idTokenEncryptedResponseEnc;

    /**
     * @ORM\Column(name="default_max_age", type="integer", length=11, nullable=true)
     * 
     * @var string $defaultMaxAge
     */
    protected $defaultMaxAge;

    /**
     * @ORM\Column(name="require_auth_time", type="boolean", nullable=true)
     * 
     * @var boolean $requireAuthTime
     */
    protected $requireAuthTime = false;

    /**
     * @ORM\Column(name="scope", type="array", nullable=true)
     * 
     * @var string $scope
     */
    protected $scope;

    public function __construct()
    {
        
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersit()
    {
        $this->clientIdIssuedAt = new \DateTime('now');
    }
    
    /**
     * getId
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * getClientId
     * 
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * getClientIdIssuedAt
     * 
     * @return \DateTime
     */
    public function getClientIdIssuedAt()
    {
        return $this->clientIdIssuedAt;
    }

    /**
     * getClientSecret
     * 
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * getClientSecretExpiresAt
     * 
     * @return \DateTime
     */
    public function getClientSecretExpiresAt()
    {
        return $this->clientSecretExpiresAt;
    }

    /**
     * getClientName
     * 
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * @return string
     */
    public function getClientUri()
    {
        return $this->clientUri;
    }

    /**
     * getContacts
     * 
     * @return array<string>
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * getApplicationType
     * 
     * @return string
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }

    /**
     * getLogoUri
     * 
     * @return string
     */
    public function getLogoUri()
    {
        return $this->logoUri;
    }

    /**
     * getTosUri
     * 
     * @return string
     */
    public function getTosUri()
    {
        return $this->tosUri;
    }

    /**
     * getPolicyUri
     * 
     * @return string
     */
    public function getPolicyUri()
    {
        return $this->policyUri;
    }

    /**
     * getRedirectUris
     * 
     * @return array<string>
     */
    public function getRedirectUris()
    {
        return $this->redirectUris;
    }

    /**
     * getPostLogoutRedirectUri
     * 
     * @return string
     */
    public function getPostLogoutRedirectUri()
    {
        return $this->postLogoutRedirectUri;
    }

    /**
     * getTokenEndpointAuthMethod
     * 
     * @return string
     */
    public function getTokenEndpointAuthMethod()
    {
        return $this->tokenEndpointAuthMethod;
    }

    /**
     * getTokenEndpointAuthSigningAlg
     * 
     * @return string
     */
    public function getTokenEndpointAuthSigningAlg()
    {
        return $this->tokenEndpointAuthSigningAlg;
    }

    /**
     * getJwksUri
     * 
     * @return string
     */
    public function getJwksUri()
    {
        return $this->jwksUri;
    }

    /**
     * getJwkEncryptionUri
     * 
     * @return string
     */
    public function getJwkEncryptionUri()
    {
        return $this->jwkEncryptionUri;
    }

    /**
     * getX509Uri
     * 
     * @return string
     */
    public function getX509Uri()
    {
        return $this->x509Uri;
    }

    /**
     * getX509EncryptionUri
     * 
     * @return string
     */
    public function getX509EncryptionUri()
    {
        return $this->x509EncryptionUri;
    }

    /**
     * getRequestObjectSigningAlg
     * 
     * @return string
     */
    public function getRequestObjectSigningAlg()
    {
        return $this->requestObjectSigningAlg;
    }

    /**
     * getUserinfoSignedResponseAlg
     * 
     * @return string
     */
    public function getUserinfoSignedResponseAlg()
    {
        return $this->userinfoSignedResponseAlg;
    }

    /**
     * getUserinfoEncryptedResponseAlg
     * 
     * @return string
     */
    public function getUserinfoEncryptedResponseAlg()
    {
        return $this->userinfoEncryptedResponseAlg;
    }

    /**
     * getUserinfoEncryptedResponseEnc
     * 
     * @return string
     */
    public function getUserinfoEncryptedResponseEnc()
    {
        return $this->userinfoEncryptedResponseEnc;
    }

    /**
     * getIdTokenSignedResponseAlg
     * 
     * @return string
     */
    public function getIdTokenSignedResponseAlg()
    {
        return $this->idTokenSignedResponseAlg;
    }

    /**
     * getIdTokenEncryptedResponseAlg
     * 
     * @return string
     */
    public function getIdTokenEncryptedResponseAlg()
    {
        return $this->idTokenEncryptedResponseAlg;
    }

    /**
     * getIdTokenEncryptedResponseEnc
     * 
     * @return string
     */
    public function getIdTokenEncryptedResponseEnc()
    {
        return $this->idTokenEncryptedResponseEnc;
    }

    /**
     * getDefaultMaxAge
     * 
     * @return integer
     */
    public function getDefaultMaxAge()
    {
        return $this->defaultMaxAge;
    }

    /**
     * getRequireAuthTime
     * 
     * @return boolean
     */
    public function getRequireAuthTime()
    {
        return $this->requireAuthTime;
    }

    /**
     * getScope
     * 
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * setId
     * 
     * @param type $id
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * setClientId
     * 
     * @param string $clientId
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * setClientIdIssuedAt
     * 
     * @param \DateTime $clientIdIssuedAt
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setClientIdIssuedAt(\DateTime $clientIdIssuedAt)
    {
        $this->clientIdIssuedAt = $clientIdIssuedAt;
        return $this;
    }

    /**
     * setClientSecret
     * 
     * @param string $clientSecret
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * setClientSecretExpiresAt
     * 
     * @param \DateTime $clientSecretExpiresAt
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setClientSecretExpiresAt(\DateTime $clientSecretExpiresAt)
    {
        $this->clientSecretExpiresAt = $clientSecretExpiresAt;
        return $this;
    }

    /**
     * setClientName
     * 
     * @param string $clientName
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;
        return $this;
    }

    /**
     * setClientUri
     * 
     * @param string $clientUri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setClientUri($clientUri)
    {
        $this->clientUri = $clientUri;
        return $this;
    }

    /**
     * setContacts
     * 
     * @param array $contacts
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setContacts(array $contacts)
    {
        $this->contacts = $contacts;
        return $this;
    }

    /**
     * setApplicationType
     * 
     * @param string $applicationType
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
        return $this;
    }

    /**
     * setLogoUri
     * 
     * @param string $logoUri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setLogoUri($logoUri)
    {
        $this->logoUri = $logoUri;
        return $this;
    }

    /**
     * setTosUri
     * 
     * @param string $tosUri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setTosUri($tosUri)
    {
        $this->tosUri = $tosUri;
        return $this;
    }

    /**
     * setPolicyUri
     * 
     * @param string $policyUri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setPolicyUri($policyUri)
    {
        $this->policyUri = $policyUri;
        return $this;
    }

    /**
     * setRedirectUris
     * 
     * @param array $redirectUris
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setRedirectUris(array $redirectUris)
    {
        $this->redirectUris = $redirectUris;
        return $this;
    }

    /**
     * setPostLogoutRedirectUri
     * 
     * @param string $postLogoutRedirectUri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setPostLogoutRedirectUri($postLogoutRedirectUri)
    {
        $this->postLogoutRedirectUri = $postLogoutRedirectUri;
        return $this;
    }

    /**
     * setTokenEndpointAuthMethod
     * 
     * @param string $tokenEndpointAuthMethod
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setTokenEndpointAuthMethod($tokenEndpointAuthMethod)
    {
        $this->tokenEndpointAuthMethod = $tokenEndpointAuthMethod;
        return $this;
    }

    /**
     * setTokenEndpointAuthSigningAlg
     * 
     * @param string $tokenEndpointAuthSigningAlg
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setTokenEndpointAuthSigningAlg($tokenEndpointAuthSigningAlg)
    {
        $this->tokenEndpointAuthSigningAlg = $tokenEndpointAuthSigningAlg;
        return $this;
    }

    /**
     * setJwksUri
     * 
     * @param string $jwksUri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setJwksUri($jwksUri)
    {
        $this->jwksUri = $jwksUri;
        return $this;
    }

    /**
     * setJwkEncryptionUri
     * 
     * @param string $jwkEncryptionUri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setJwkEncryptionUri($jwkEncryptionUri)
    {
        $this->jwkEncryptionUri = $jwkEncryptionUri;
        return $this;
    }

    /**
     * setX509Uri
     * 
     * @param string $x509Uri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setX509Uri($x509Uri)
    {
        $this->x509Uri = $x509Uri;
        return $this;
    }

    /**
     * setX509EncryptionUri
     * 
     * @param string $x509EncryptionUri
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setX509EncryptionUri($x509EncryptionUri)
    {
        $this->x509EncryptionUri = $x509EncryptionUri;
        return $this;
    }

    /**
     * setRequestObjectSigningAlg
     * 
     * @param string $requestObjectSigningAlg
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setRequestObjectSigningAlg($requestObjectSigningAlg)
    {
        $this->requestObjectSigningAlg = $requestObjectSigningAlg;
        return $this;
    }

    /**
     * setUserinfoSignedResponseAlg
     * 
     * @param string $userinfoSignedResponseAlg
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setUserinfoSignedResponseAlg($userinfoSignedResponseAlg)
    {
        $this->userinfoSignedResponseAlg = $userinfoSignedResponseAlg;
        return $this;
    }

    /**
     * setUserinfoEncryptedResponseAlg
     * 
     * @param string $userinfoEncryptedResponseAlg
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setUserinfoEncryptedResponseAlg($userinfoEncryptedResponseAlg)
    {
        $this->userinfoEncryptedResponseAlg = $userinfoEncryptedResponseAlg;
        return $this;
    }

    /**
     * setUserinfoEncryptedResponseEnc
     * 
     * @param string $userinfoEncryptedResponseEnc
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setUserinfoEncryptedResponseEnc($userinfoEncryptedResponseEnc)
    {
        $this->userinfoEncryptedResponseEnc = $userinfoEncryptedResponseEnc;
        return $this;
    }

    /**
     * setIdTokenSignedResponseAlg
     * 
     * @param string $idTokenSignedResponseAlg
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setIdTokenSignedResponseAlg($idTokenSignedResponseAlg)
    {
        $this->idTokenSignedResponseAlg = $idTokenSignedResponseAlg;
        return $this;
    }

    /**
     * setIdTokenEncryptedResponseAlg
     * 
     * @param string $idTokenEncryptedResponseAlg
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setIdTokenEncryptedResponseAlg($idTokenEncryptedResponseAlg)
    {
        $this->idTokenEncryptedResponseAlg = $idTokenEncryptedResponseAlg;
        return $this;
    }

    /**
     * setIdTokenEncryptedResponseEnc
     * 
     * @param string $idTokenEncryptedResponseEnc
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setIdTokenEncryptedResponseEnc($idTokenEncryptedResponseEnc)
    {
        $this->idTokenEncryptedResponseEnc = $idTokenEncryptedResponseEnc;
        return $this;
    }

    /**
     * setDefaultMaxAge
     * 
     * @param integer $defaultMaxAge
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setDefaultMaxAge($defaultMaxAge)
    {
        $this->defaultMaxAge = $defaultMaxAge;
        return $this;
    }

    /**
     * setRequireAuthTime
     * 
     * @param boolean $requireAuthTime
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setRequireAuthTime($requireAuthTime)
    {
        $this->requireAuthTime = (bool) $requireAuthTime;
        return $this;
    }

    /**
     * addToken
     * 
     * @param Token $token
     * @return \Cnerta\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function addToken(Token $token)
    {
        // if the object isn't in the list
        if (!$this->tokenList->contains($token)) {
            $this->tokenList->add($token);
            $token->setClient($this);
        }

        return $this;
    }

    /**
     * setToken
     * 
     * @param Mix (Collection|Token) $items
     * @return \Cnerta\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setToken($items = null)
    {
        if ($items instanceof Collection) {
            foreach ($items as $item) {
                $this->addToken($item);
            }
        } elseif ($items instanceof Token) {
            $this->addToken($items);
        } elseif ($items === null) {
            $this->tokenList = new ArrayCollection();
        } else {
            throw new \Exception('$items must be an instance of Token or Collection');
        }
        return $this;
    }

    /**
     * setScope
     * 
     * @param array $scope
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\Client
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * getTokenList
     * 
     * @return ArrayCollection<Token> $tokenList 
     */
    public function getTokenList()
    {
        return $this->tokenList;
    }

    public function eraseCredentials()
    {
        
    }

    public function getPassword()
    {
        return $this->getClientSecret();
    }

    public function getRoles()
    {
        return array('ROLE_OIC_CLIENT');
    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->getClientId();
    }

}
