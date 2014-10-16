<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Provider;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Waldo\OpenIdConnect\LdapProviderBundle\Manager\LdapManagerUserInterface;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;

/**
 * LDAP User Provider
 *
 * @author Boris Morel
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class LdapUserProvider implements UserProviderInterface
{
    
    /**
     * @var \Waldo\OpenIdConnect\LdapProviderBundle\Manager\LdapManagerUserInterface
     */
    private $ldapManager;
    
    /**
     * The class name of the User model
     * @var string
     */
    private $userClass;

    /**
     * Constructor
     * 
     * @param LdapManagerUserInterface $ldapManager
     * @param string $userClass
     */
    public function __construct(LdapManagerUserInterface $ldapManager, $userClass)
    {
        $this->ldapManager = $ldapManager;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        // Throw the exception if the username is not provided.
        if (empty($username)) {
            throw new UsernameNotFoundException('The username is not provided.');
        }

        $ldapUser = $this->searchByUsername($username);

        return $ldapUser;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Account) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return is_subclass_of($class, '\Waldo\OpenIdConnect\ModelBundle\Entity\Account');
    }

    private function searchByUsername($username)
    {
        $this->ldapManager->exists($username);

        $lm = $this->ldapManager
            ->setUsername($username)
            ->doPass();

        /* @var $ldapUser \Waldo\OpenIdConnect\ModelBundle\Entity\Account */
        $ldapUser = new $this->userClass();

        $ldapUser
            ->setUsername($lm->getUsername())
            ->setEnabled(true)
            ->setEmail($lm->getEmail())
            ->setRoles($lm->getRoles())
            ->setExternalId($lm->getDn())
            ->setName($lm->getCn())
//            ->setAttributes($lm->getAttributes())
            ->setGivenName($lm->getGivenName())
            ->setNickname($lm->getSurname())
            ->setPreferedUsername($lm->getDisplayName())
            ;

        return $ldapUser;
    }
}
