<?php

namespace Waldo\OpenIdConnect\ProviderBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Description of LoginListener
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class LoginListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin'
        );
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if($event->getAuthenticationToken()->isAuthenticated()) {
            // Set a datetime after the user as logged in
            $event
                    ->getAuthenticationToken()
                    ->setAttribute("ioc.token.issuedAt", new \DateTime('now'));
        }
    }

}
