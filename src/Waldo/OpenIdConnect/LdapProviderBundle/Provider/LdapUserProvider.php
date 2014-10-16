<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Provider;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Waldo\OpenIdConnect\LdapProviderBundle\Manager\LdapManagerUserInterface;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\LdapProviderBundle\Exception\DuplicateUsernameException;
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

    
    private $validator;

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
    public function __construct(LdapManagerUserInterface $ldapManager, EntityManager $em, $validator, $userClass)
    {
        $this->ldapManager = $ldapManager;
        $this->em = $em;
        $this->validator = $validator;

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

        /* @var $errors \Symfony\Component\Validator\ConstraintViolationList */
        $errors = $this->validator->validate($ldapUser, array('uniqueUsername'));

        if($errors->count() > 0) {
            throw new DuplicateUsernameException(sprintf('The username "%s" is already in use', $ldapUser->getUsername()));
        }

        if ($dbUser === null) {
            $this->em->persist($ldapUser);
            $this->em->flush();
            
            return $dbUser;
        }
        
        $this->updateUser($dbUser, $ldapUser);
        
        return $dbUser;
    }
    
    
    public function updateUser($persisted, $newUser)
    {
        // Check if the properties are equal
        $isEqual = $this->areEquals($newUser, $persisted);
        
        if($isEqual) {
            $persisted
            ->setUsername($newUser->getUsername())
            ->setEmail($newUser->getEmail())
            ->setRoles($newUser->getRoles())
            ->setExternalId($newUser->getExternalId())
            ->setName($newUser->getName())
            ->setGivenName($newUser->getGivenName())
            ->setNickname($newUser->getNickname())
            ->setPreferedUsername($newUser->getPreferedUsername())
            ;
            
            $this->em->persist($persisted);
            $this->em->flush();
        }
    }
    
    private function areEquals($user1, $user2) {
        return $user1->getUsername() !== $user2->getUsername()
                || $user1->getEmail() !== $user2->getEmail()
                || $user1->getRoles() !== $user2->getRoles()
                || $user1->getExternalId() !== $user2->getExternalId()
                || $user1->getName() !== $user2->getName()
                || $user1->getGivenName() !== $user2->getGivenName()
                || $user1->getNickname() !== $user2->getNickname()
                || $user1->getPreferedUsername() !== $user2->getPreferedUsername()
                ;
    }
}
