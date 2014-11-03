<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Services;

/**
 * LostPasswordManagerInterface
 * 
 * Interface to add behavior to recover a password based on a username / email
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
interface LostPasswordManagerInterface
{

    /**
     * Do an action or not for the given username or email
     * 
     * @param string $usernameOrEmail
     * @return bool True if the user has been found and an action has been done, fasle otherwise
     */
    public function handleLostPassword($usernameOrEmail);
    
}
