<?php

namespace Waldo\OpenIdConnect\ProviderBundle\EventListener;

use Waldo\OpenIdConnect\ProviderBundle\Entity\AccountAction;
use Waldo\OpenIdConnect\ProviderBundle\Events\OICProviderEvent;
use Waldo\OpenIdConnect\ProviderBundle\Events\AccountEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Waldo\OpenIdConnect\ProviderBundle\Services\AccountActionsService;

/**
 * Description of AccountListener
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AccountListener implements EventSubscriberInterface
{
    /**
     * @var AccountActionsService
     */
    protected $accountActions;

    
    public function __construct(AccountActionsService $accountActions)
    {
        $this->accountActions = $accountActions;
    }

    
    public static function getSubscribedEvents()
    {
        return array(
            OICProviderEvent::AFTER_SAVE_ACCOUNT => 'sendMailRegistrationConfirmation'
        );
    }

    public function sendMailRegistrationConfirmation(AccountEvent $event)
    {
        
        $this->accountActions->sendMailRegistrationConfirmation($event->getAccount());
        
    }

}
