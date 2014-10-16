<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Manager;

use Waldo\OpenIdConnect\LdapProviderBundle\Exception\DuplicateUsernameException;
use Doctrine\ORM\EntityManager;

class UserManager
{
    /**
     * @var EntityManager
     */
    private $em;
    
    private $validator;
    
    private $userClass;

    public function __construct(EntityManager $em, $validator, $userClass)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->userClass = $userClass;
    }

    
    public function registerUser($ldapUser)
    {

        $dbUser = $this->em->getRepository($this->userClass)
                ->findOneBy(array(
            'externalId' => $ldapUser->getExternalId(),
            'providerName' => 'OIC-LDAP'
        ));


        if ($dbUser === null) {
            
            /* @var $errors \Symfony\Component\Validator\ConstraintViolationList */
            $errors = $this->validator->validate($ldapUser, array('uniqueUsername'));

            if ($errors->count() > 0) {

                $this->mergeUser($ldapUser);

                $message = <<<EOF
Your account from LDAP has been merge with your actual account.
Now you can use your LDAP username : %s, and your LDAP password.
EOF;
                throw new DuplicateUsernameException(sprintf($message, $ldapUser->getUsername()));
            }
            
            $ldapUser
                    ->setSub(hash("sha512", sprintf('%s%s', $ldapUser->getUsername(), time())))
                    ->setProviderName('OIC-LDAP');
            $this->em->persist($ldapUser);
            $this->em->flush();

            return $ldapUser;
        }

        $this->updateUser($dbUser, $ldapUser);

        return $dbUser;
    }

    public function updateUser($persisted, $newUser)
    {
        // Check if the properties are equal
        $isEqual = $this->areEquals($newUser, $persisted);

        if ($isEqual) {
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
    
    private function mergeUser($user)
    {
        /* @var $dbUser \Waldo\OpenIdConnect\ModelBundle\Entity\Account */
        $dbUser = $this->em->getRepository($this->userClass)
                ->findOneByEmail($user->getEmail());
        
        $dbUser->setProviderName('OIC-LDAP')
               ->setPassword(null);
        
        $this->updateUser($dbUser, $user);        
    }

    private function areEquals($user1, $user2)
    {
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
