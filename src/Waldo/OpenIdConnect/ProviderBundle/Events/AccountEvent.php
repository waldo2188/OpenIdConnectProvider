<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Events;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Account;

use Symfony\Component\EventDispatcher\Event;

/**
 * Description of AccountEvent
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AccountEvent extends Event
{
    /**
     *
     * @var Waldo\OpenIdConnect\ProviderBundle\Entity\Account
     */
    private $account;
    
    /**
     * @return Waldo\OpenIdConnect\ProviderBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
    }
}
