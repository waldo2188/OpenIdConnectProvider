<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;

/**
 * UniqueUsernameValidatorInterface
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
interface UniqueUsernameValidatorInterface
{
    /**
     * Check if user with this username exist
     * 
     * @param string $username
     * @param Account $account
     * @return boolean 
     */
    public function exist($username, Account $account);
}
