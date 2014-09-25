<?php

namespace Waldo\OpenIdConnect\EnduserBundle\EventListener;

use Waldo\OpenIdConnect\EnduserBundle\Events\OICProviderEvent;
use Waldo\OpenIdConnect\EnduserBundle\Events\AccountEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Waldo\OpenIdConnect\EnduserBundle\Services\AccountActionsService;

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
