<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Waldo\OpenIdConnect\ProviderBundle\Entity\AccountInterface;

/**
 * Account implement standard Claims
 * Account represent an enduser.
 * 
 * @see http://openid.net/specs/openid-connect-core-1_0.html#StandardClaims
 * 
 * @ORM\Entity() 
 * @ORM\Table(name="account")
 * @ORM\HasLifecycleCallbacks
 */
class Account implements AccountInterface, \Serializable
{

    const SCOPE_PROFILE = 'profile';
    const SCOPE_EMAIL = 'email';
    const SCOPE_ADDRESS = 'address';
    const SCOPE_PHONE = 'phone';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, unique=true)
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Token", mappedBy="account", cascade={"persist", "remove", "merge"})
     * 
     * @var ArrayCollection<Token> $tokenList
     */
    protected $tokenList;

    /**
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     * 
     * @var string $username Username used by enduser to log in
     */
    protected $username;

    /**
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     * 
     * @var string $salt Salt for use for encode password
     */
    protected $salt;

    /**
     * @ORM\Column(name="roles", type="array", nullable=true)
     * 
     * @var string $roles Roles defined for enduser
     */
    protected $roles;

    /**
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     * 
     * @var string $password Password used by enduser to log in
     */
    protected $password;

    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     * 
     * @var boolean $enabled True if the account is enabled; otherwise false 
     */
    protected $enabled = false;

    /**
     * @ORM\Column(name="expiration", type="datetime", nullable=true)
     * 
     * @var \DateTime $expiration Datetime where this account expire
     */
    protected $expiration;

    /**
     * @ORM\Column(name="locked", type="boolean")
     * 
     * @var boolean $locked True if this account has been locked; otherwise false 
     */
    protected $locked = false;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * 
     * @var string $name Full name 
     */
    protected $name;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="given_name", type="string", length=255, nullable=true)
     * 
     * @var string $givenName Given name(s) or first name(s) 
     */
    protected $givenName;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="family_name", type="string", length=255, nullable=true)
     * 
     * @var string $familyName Surname(s) or last name(s)
     */
    protected $familyName;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
     * 
     * @var string $middleName Middle name(s) 
     */
    protected $middleName;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="nickname", type="string", length=255, nullable=true)
     * 
     * @var string $nickname Casual name 
     */
    protected $nickname;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="prefered_username", type="string", length=255, nullable=true)
     * 
     * @var string $preferedUsername Shorthand name by which the End-User wishes to be referred to 
     */
    protected $preferedUsername;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="profile", type="string", length=255, nullable=true)
     * 
     * @var string $profile Profile page URL
     */
    protected $profile;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="picture", type="string", length=2048, nullable=true)
     * 
     * @var string $picture Profile picture URL 
     */
    protected $picture;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="website", type="string", length=2048, nullable=true)
     * 
     * @var string $website Web page or blog URL 
     */
    protected $website;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     * 
     * @var string $gender Gender
     */
    protected $gender;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="birthdate", type="datetime", nullable=true)
     * 
     * @var \DateTime $birthdate Birthday 
     */
    protected $birthdate;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="zone_info", type="string", length=255, nullable=true)
     * 
     * @var string $zoneInfo Time zone (France/Paris)
     */
    protected $zoneInfo;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="locale", type="string", length=255, nullable=true)
     * 
     * @var string $locale Locale (en, fr, FR_ca)
     */
    protected $locale;

    /**
     * Scope: profile
     * 
     * @ORM\Column(name="update_at", type="datetime", nullable=true)
     * 
     * @var \DateTime $updateAt
     */
    protected $updateAt;
    
    /**
     * Scope: profile
     * 
     * @ORM\Column(name="last_login_at", type="datetime", nullable=true)
     * 
     * @var \DateTime $lastLoginAt
     */
    protected $lastLoginAt;

    /**
     * Scope: email
     * 
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * 
     * @var string $email Preferred e-mail address 
     */
    protected $email;

    /**
     * Scope: email
     * 
     * @ORM\Column(name="email_verified", type="boolean", nullable=true)
     * 
     * @var boolean $emailVerified True if the e-mail address has been verified; otherwise false 
     */
    protected $emailVerified = false;

    /**
     * Scope: phone
     * 
     * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
     * 
     * @var string $phoneNumber Preferred telephone number 
     */
    protected $phoneNumber;

    /**
     * Scope: phone
     * 
     * @ORM\Column(name="phone_number_verified", type="boolean", nullable=true)
     * 
     * @var boolean $phoneNumberVerified True if the phone number has been verified; otherwise false 
     */
    protected $phoneNumberVerified = false;

    /**
     * Scope: address
     * 
     * @ORM\Column(name="address", type="object", nullable=true)
     * 
     * @see http://openid.net/specs/openid-connect-core-1_0.html#AddressClaim
     * @var Address $address Preferred postal address 
     */
    protected $address;

    public static $scopeProfile = array(
                'name',
                'givenName',
                'familyName',
                'middleName',
                'nickname',
                'preferedUsername',
                'profile',
                'picture',
                'website',
                'gender',
                'birthdate',
                'zoneInfo',
                'locale',
                'updateAt'
                );
            
    public static  $scopeEmail = array(
                'email',
                'emailVerified'
            );
    
    public static  $scopePhone = array('phoneNumber', 'phoneNumberVerified');
    
    public static  $scopeAddress = array('address');
    
    public function __construct()
    {
        $this->roles = array();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersit()
    {
        $this->updateDate = new \DateTime('now');
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
     * getUsername
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * getPassword
     * 
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * getEnabled
     * 
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * getExpiration
     * 
     * @return \DateTime
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * getLocked
     * 
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * getName
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getGivenName
     * 
     * @return string
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * getFamilyName
     * 
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * getMiddleName
     * 
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * getNickname
     * 
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * getPreferedUsername
     * 
     * @return string
     */
    public function getPreferedUsername()
    {
        return $this->preferedUsername;
    }

    /**
     * getProfile
     * 
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * getPicture
     * 
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * getWebsite
     * 
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * getEmail
     * 
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * getEmailVerified
     * 
     * @return boolean
     */
    public function getEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * getGender
     * 
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * getBirthdate
     * 
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * getZoneInfo
     * 
     * @return string
     */
    public function getZoneInfo()
    {
        return $this->zoneInfo;
    }

    /**
     * getLocale
     * 
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * getPhoneNumber
     * 
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * getPhoneNumberVerified
     * 
     * @return boolean
     */
    public function getPhoneNumberVerified()
    {
        return $this->phoneNumberVerified;
    }

    /**
     * getAddress
     * 
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * getUpdateAt
     * 
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * setId
     * 
     * @param integer $id
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * setUsername
     * 
     * @param string $username
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * setPassword
     * 
     * @param string $password
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * setEnabled
     * 
     * @param boolean $enabled
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;
        return $this;
    }

    /**
     * setExpiration
     * 
     * @param \DateTime $expiration
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setExpiration(\DateTime $expiration)
    {
        $this->expiration = $expiration;
        return $this;
    }

    /**
     * setLocked
     * 
     * @param boolean $locked
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setLocked($locked)
    {
        $this->locked = (bool) $locked;
        return $this;
    }

    /**
     * setName
     * 
     * @param string $name
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * setGivenName
     * 
     * @param string $givenName
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
        return $this;
    }

    /**
     * setFamilyName
     * 
     * @param string $familyName
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
        return $this;
    }

    /**
     * setMiddleName
     * 
     * @param string $middleName
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * setNickname
     * 
     * @param string $nickname
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
        return $this;
    }

    /**
     * setPreferedUsername
     * 
     * @param string $preferedUsername
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setPreferedUsername($preferedUsername)
    {
        $this->preferedUsername = $preferedUsername;
        return $this;
    }

    /**
     * setProfile
     * 
     * @param string $profile
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * setPicture
     * 
     * @param string $picture
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * setWebsite
     * 
     * @param string $website
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * setEmail
     * 
     * @param string $email
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * setEmailVerified
     * 
     * @param boolean $emailVerified
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setEmailVerified($emailVerified)
    {
        $this->emailVerified = (bool) $emailVerified;
        return $this;
    }

    /**
     * setGender
     * 
     * @param string $gender
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * setBirthdate
     * 
     * @param \DateTime $birthdate
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setBirthdate(\DateTime $birthdate)
    {
        $this->birthdate = $birthdate;
        return $this;
    }

    /**
     * setZoneInfo
     * 
     * @param string $zoneInfo
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setZoneInfo($zoneInfo)
    {
        $this->zoneInfo = $zoneInfo;
        return $this;
    }

    /**
     * setLocale
     * 
     * @param string $locale
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * setPhoneNumber
     * 
     * @param string $phoneNumber
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * setPhoneNumberVerified
     * 
     * @param boolean $phoneNumberVerified
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setPhoneNumberVerified($phoneNumberVerified)
    {
        $this->phoneNumberVerified = (bool) $phoneNumberVerified;
        return $this;
    }

    /**
     * setAddress
     * 
     * @param \Cnerta\OpenIdConnect\ProviderBundle\Entity\Address $address
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setAddress(Address $address = null)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * setUpdateAt
     * 
     * @param \DateTime $updateAt
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setUpdateAt(\DateTime $updateAt)
    {
        $this->updateAt = $updateAt;
        return $this;
    }

    /**
     * 
     * @param \DateTime $lastLoginAt
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setLastLoginAt(\DateTime $lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;
        return $this;
    }

    /**
     * addToken
     * 
     * @param Token $token
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function addToken(Token $token)
    {
        // if the object isn't in the list
        if (!$this->tokenList->contains($token)) {
            $this->tokenList->add($token);
            $token->setAccount($this);
        }

        return $this;
    }

    /**
     * setToken
     * 
     * @param Mix (Collection|Token) $items
     * @return \Cnerta\OpenIdConnect\ProviderBundle\Entity\Account
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
     * getTokenList
     * 
     * @return ArrayCollection<Token> $tokenList 
     */
    public function getTokenList()
    {
        return $this->tokenList;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        
    }

    /**
     * setRoles
     * 
     * @param array $roles
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * addRole
     * 
     * @param string $role
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function addRole($role)
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * setSalt
     * 
     * @param string $salt
     * @return \Waldo\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired()
    {
        return $this->expiration < new \DateTime('now');
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->enabled,
            $this->locked
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
                $this->id,
                $this->username,
                $this->enabled,
                $this->locked
                ) = unserialize($serialized);
    }

}
