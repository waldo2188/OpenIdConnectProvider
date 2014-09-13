<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Waldo\OpenIdConnect\ProviderBundle\EntityRepository\TokenRepository")
 * @ORM\Table(name="token")
 * @ORM\HasLifecycleCallbacks
 */
class Token
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="tokenList", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     * })
     *
     * @var Account $account 
     */
    protected $account;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="tokenList", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     *
     * @var Client $client 
     */
    protected $client;

    /**
     * @ORM\Column(name="code_token", type="string", length=255, nullable=true)
     * 
     * @var string $codeToken
     */
    protected $codeToken;

    /**
     * @ORM\Column(name="access_token", type="string", length=255, nullable=true)
     * 
     * @var string $accessToken
     */
    protected $accessToken;

    /**
     * @ORM\Column(name="refresht_token", type="string", length=255, nullable=true)
     * 
     * @var string $refreshToken
     */
    protected $refreshToken;

    /**
     * @ORM\Column(name="scope", type="array", nullable=true)
     * 
     * @var string $scope
     */
    protected $scope;

    /**
     * @ORM\Column(name="redirect_uri", type="string", nullable=true)
     * 
     * @var string $redirectUri
     */
    protected $redirectUri;

    /**
     * @ORM\Column(name="nonce", type="string", nullable=true)
     * 
     * @var string $nonce
     */
    protected $nonce;

    /**
     * @ORM\Column(name="issued_at", type="datetime", length=255, nullable=true)
     * 
     * @var \DateTime $issuedAt
     */
    protected $issuedAt;

    /**
     * @ORM\Column(name="expiration_at", type="datetime", length=255, nullable=true)
     * 
     * @var \DateTime $expirationAt
     */
    protected $expirationAt;

    /**
     * getAccount
     * 
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * getClient
     * 
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * getCodeToken
     * 
     * @return string 
     */
    public function getCodeToken()
    {
        return $this->codeToken;
    }

    /**
     * getAccessToken
     * 
     * @return string 
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * getRefreshToken
     * 
     * @return string 
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * getIssedAt
     * 
     * @return \DateTime 
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * getExpirationAt
     * 
     * @return \DateTime  
     */
    public function getExpirationAt()
    {
        return $this->expirationAt;
    }

    /**
     * 
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
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
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * setAccount
     * 
     * @param \Waldo\OpenIdConnect\ProviderBundle\Entity\Account $account
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * setClient
     * 
     * @param \Waldo\OpenIdConnect\ProviderBundle\Entity\Client $client
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * setCodeToken
     * 
     * @param string $codeToken
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setCodeToken($codeToken = null)
    {
        $this->codeToken = $codeToken;
        return $this;
    }

    /**
     * setAccessToken
     * 
     * @param string $accessToken
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setAccessToken($accessToken = null)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * setRefreshToken
     * 
     * @param string $refreshToken
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setRefreshToken($refreshToken = null)
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    /**
     * setIssedAt
     * 
     * @param \DateTime $issedAt
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setIssuedAt(\DateTime $issedAt)
    {
        $this->issuedAt = $issedAt;
        return $this;
    }

    /**
     * setExpirationAt
     * 
     * @param \DateTime $expirationAt
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setExpirationAt(\DateTime $expirationAt)
    {
        $this->expirationAt = $expirationAt;
        return $this;
    }

    /**
     * 
     * @param array $scope
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setScope(array $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * 
     * @param string $redirectUri
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    /**
     * 
     * @param string $nonce
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Token
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
        return $this;
    }

}
