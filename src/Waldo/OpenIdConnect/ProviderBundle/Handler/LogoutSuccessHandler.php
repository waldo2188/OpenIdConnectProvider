<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * LogoutSuccessHandler
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    protected $httpUtils;
    protected $targetUrl;

    /**
     * @param HttpUtils $httpUtils
     * @param string    $targetUrl
     */
    public function __construct(HttpUtils $httpUtils, $targetUrl = '/')
    {
        $this->httpUtils = $httpUtils;

        $this->targetUrl = $targetUrl;
    }

    
    public function onLogoutSuccess(Request $request)
    {
        $request->getSession()->remove("oic.login.auth.user");

        return $this->httpUtils->createRedirectResponse($request, $this->targetUrl);
    }

}
