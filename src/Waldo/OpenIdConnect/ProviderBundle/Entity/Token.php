<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity() 
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
     * @ORM\Column(name="codeToken", type="string", length=255, nullable=true)
     * 
     * @var string $codeToken
     */
    protected $codeToken;

    /**
     * @ORM\Column(name="accessToken", type="string", length=255, nullable=true)
     * 
     * @var string $accessToken
     */
    protected $accessToken;

    /**
     * @ORM\Column(name="refreshToken", type="string", length=255, nullable=true)
     * 
     * @var string $refreshToken
     */
    protected $refreshToken;

    /**
     * @ORM\Column(name="issedAt", type="datetime", length=255, nullable=true)
     * 
     * @var \DateTime $issedAt
     */
    protected $issedAt;

    /**
     * @ORM\Column(name="expirationAt", type="datetime", length=255, nullable=true)
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
    public function getIssedAt()
    {
        return $this->issedAt;
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
    public function setCodeToken($codeToken)
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
    public function setAccessToken($accessToken)
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
    public function setRefreshToken($refreshToken)
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
    public function setIssedAt(\DateTime $issedAt)
    {
        $this->issedAt = $issedAt;
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

}

