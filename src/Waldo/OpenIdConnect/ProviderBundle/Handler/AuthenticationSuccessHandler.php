<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Handler;

use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * AuthenticationSuccessHandler
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    /**
     * @var SecurityContextInterface 
     */
    protected $securityContext;

    /**
     * @var HttpUtils 
     */
    protected $httpUtils;

    public function __construct(SecurityContextInterface $securityContext, HttpUtils $httpUtils)
    {
        $this->securityContext = $securityContext;
        $this->httpUtils = $httpUtils;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($request->query->has('client_id')) {
            return $this->httpUtils->createRedirectResponse(
                            new Request(array(), array(), 
                                    array('clientId' => $request->query->get('client_id'))),
                        "oicp_authentication_scope"
            );
        }

        $sessionName = sprintf('_security.%s.target_path', $token->getProviderKey());

        if ($request->getSession()->has($sessionName)) {
            return $this->httpUtils->createRedirectResponse(
                            new Request(), $request->getSession()->get($sessionName)
            );
        }

        if ($this->securityContext->isGranted('ROLE_ADMIN')) {
            return $this->httpUtils->createRedirectResponse(
                            new Request(), "oicp_admin_index"
            );
        }


        return $this->httpUtils->createRedirectResponse(
                        new Request(), "oicp_account_index"
        );
    }

}
