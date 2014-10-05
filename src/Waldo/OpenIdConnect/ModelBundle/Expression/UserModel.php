<?php

namespace Waldo\OpenIdConnect\ModelBundle\Expression;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;

/**
 * Description of UserModel
 *
 */
class UserModel
{
    public $username;
    public $email;
    public $name;
    public $given_name;
    public $family_name;
    public $middle_name;
    public $nickname;
    public $prefered_username;
            
    public function __construct(Account $account = null)
    {
        if($account !== null) {
           $this->username = $account->getUsername();
           $this->email = $account->getEmail();
           $this->name = $account->getName();
           $this->given_name = $account->getGivenName();
           $this->family_name = $account->getFamilyName();
           $this->middle_name = $account->getMiddleName();
           $this->nickname = $account->getNickname();
           $this->prefered_username = $account->getPreferedUsername();
        }
    }
}
