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
     * $account is used to exclude the actual user of the request if it was allredy stored
     * 
     * @param string $username
     * @param Account $account
     * @return boolean 
     */
    public function exist($username, Account $account);
}
