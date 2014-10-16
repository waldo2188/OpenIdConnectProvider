<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Exception;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class DuplicateUsernameException extends BadCredentialsException
{
}
