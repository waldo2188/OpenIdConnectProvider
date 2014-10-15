<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Provider;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Waldo\OpenIdConnect\LdapProviderBundle\Manager\LdapManagerUserInterface;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Doctrine\ORM\EntityManager;

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
     * @var EntityManager
     */
    private $em;


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
    public function __construct(LdapManagerUserInterface $ldapManager, EntityManager $em, $userClass)
    {
        $this->ldapManager = $ldapManager;
        $this->em = $em;
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

    private function simpleUser($username)
    {
        $ldapUser = new $this->userClass;
        $ldapUser->setUsername($username);

        return $ldapUser;
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
    
    public function manageDatabaseUser($ldapUser)
    {
        
        $dbUser = $this->em->getRepository($this->userClass)
                ->findOneBy(array(
                    'externalId' => $ldapUser->getExternalId(),
                    'providerName' => 'OIC-LDAP'
                ));
        
        if($dbUser === null) {
            $this->em->persist($ldapUser);
            $this->em->flush();
            
            return $dbUser;
        } else {
            $this->manageUserUpdate($ldapUser, $dbUser);
        }
        
        // Check if the properties are equal
        $isEqual = $ldapUser->getUsername() !== $dbUser->getUsername()
                || $ldapUser->getEmail() !== $dbUser->getEmail()
                || $ldapUser->getRoles() !== $dbUser->getRoles()
                || $ldapUser->getExternalId() !== $dbUser->getExternalId()
                || $ldapUser->getName() !== $dbUser->getName()
                || $ldapUser->getGivenName() !== $dbUser->getGivenName()
                || $ldapUser->getNickname() !== $dbUser->getNickname()
                || $ldapUser->getPreferedUsername() !== $dbUser->getPreferedUsername()
                ;
        
        if($isEqual) {
            $dbUser
            ->setUsername($ldapUser->getUsername())
            ->setEmail($ldapUser->getEmail())
            ->setRoles($ldapUser->getRoles())
            ->setExternalId($ldapUser->getExternalId())
            ->setName($ldapUser->getName())
            ->setGivenName($ldapUser->getGivenName())
            ->setNickname($ldapUser->getNickname())
            ->setPreferedUsername($ldapUser->getPreferedUsername())
            ;
            
            $this->em->persist($dbUser);
            $this->em->flush();
        }
        
        return $dbUser;
    }

}
