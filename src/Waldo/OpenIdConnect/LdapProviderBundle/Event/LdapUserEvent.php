<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;

class LdapUserEvent extends Event
{
    private $user;

    public function __construct(Account $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
