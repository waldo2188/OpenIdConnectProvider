<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Validator\Checker;

use Waldo\OpenIdConnect\ModelBundle\Validator\UniqueUsernameValidatorInterface;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\LdapProviderBundle\Manager\LdapManagerUserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;


/**
 * UniqueUsernameValidator
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UniqueUsernameValidator implements UniqueUsernameValidatorInterface
{
    /**
     * @var LdapManagerUserInterface
     */
    protected $ldap;

    public function __construct(LdapManagerUserInterface $ldap)
    {
        $this->ldap = $ldap;
    }

    public function exist($username, Account $account)
    {
        try {
            
            if($this->ldap->existsEmail($username)) {
                return $this->ldap->getDn() !== $account->getExternalId();               
            }
                
        } catch (\RuntimeException $e) {
            return false;
        } catch (UsernameNotFoundException $e) {
            return true;
        }
    }

}
