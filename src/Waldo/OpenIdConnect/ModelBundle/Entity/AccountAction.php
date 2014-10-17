<?php

namespace Waldo\OpenIdConnect\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class store token for validating user email or when user have lost 
 * his account
 * 
 * @ORM\Entity(repositoryClass="Waldo\OpenIdConnect\ModelBundle\EntityRepository\AccountActionRepository")
 * @ORM\Table(name="account_action")
 * @ORM\HasLifecycleCallbacks
 */
class AccountAction
{

    const ACTION_EMAIL_VALIDATION = 1;
    const ACTION_EMAIL_CHANGE_VALIDATION = 2;
    const ACTION_ACCOUNT_LOST = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, unique=true)
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Account", inversedBy="accountAction", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     * 
     * @var Account $account 
     */
    protected $account;

    /**
     * @ORM\Column(name="issued_at", type="datetime", length=255, nullable=false)
     * 
     * @var \DateTime $issuedAt
     */
    protected $issuedAt;

    /**
     * @ORM\Column(name="type", type="integer", length=2, nullable=false)
     * 
     * @var integer $type type
     */
    protected $type;

    /**
     * @ORM\Column(name="token", type="string", length=255, nullable=false)
     * 
     * @var string $token token
     */
    protected $token;
    
    /**
     * @ORM\Column(name="extended_data", type="array", nullable=false)
     * 
     * @var string $token token
     */
    protected $extendedData;

    public function __construct()
    {
        $this->extendedData = array();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersit()
    {
        $this->issuedAt = new \DateTime('now');
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return \DateTime
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 
     * @return type
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 
     * @return array
     */
    public function getExtendedData($key = null)
    {
        if($key === null) {
            return $this->extendedData;
        }
        
        if(array_key_exists($key, $this->extendedData)) {
            return $this->extendedData[$key];
        }
        
        return null;
    }

    
    /**
     * @param integer $id
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\Account $account
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @param \DateTime $issuedAt
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction
     */
    public function setIssuedAt(\DateTime $issuedAt)
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    /**
     * @param string $token
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 
     * @param type $type
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function addExtendedData($key, $value)
    {
        if(!in_array($value, $this->extendedData)) {
            $this->extendedData[$key] = $value;
        }
        return $this;
    }

    public function setExtendedData($extendedData = null)
    {
        foreach($extendedData as $key => $value) {
            $this->addExtendedData($key, $value);
        }
        
        if($extendedData === null) {
            $this->extendedData = array();
        }
        
        return $this;
    }


}
