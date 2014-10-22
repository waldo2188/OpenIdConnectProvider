<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Handler;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * AuthenticationSuccessHandler
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        /* @var $redirectResponse \Symfony\Component\HttpFoundation\RedirectResponse */
        $redirectResponse = parent::onAuthenticationFailure($request, $exception);
        
        if($request->query->has('client_id')) {
            $request->attributes->set('clientId', $request->query->get('client_id'));
        }
        
        $redirectResponse->setTargetUrl($this->httpUtils->createRedirectResponse($request, "login")->getTargetUrl());
        
        return $redirectResponse;
    }
}
