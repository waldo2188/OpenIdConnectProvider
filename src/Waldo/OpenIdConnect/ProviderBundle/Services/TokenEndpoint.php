<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ProviderBundle\Exception\TokenRequestException;
use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ProviderBundle\Services\IdTokenHelper;
use Waldo\OpenIdConnect\ProviderBundle\Utils\TokenCodeGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Doctrine\ORM\EntityManager;

/**
 * TokenEndpoint
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class TokenEndpoint
{

    /**
     * @var SecurityContext 
     */
    protected $securityContext;

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var IdTokenHelper 
     */
    protected $idTokenHelper;
    private $tokenRepository;

    public function __construct(SecurityContextInterface $securityContext, EntityManager $em, IdTokenHelper $idTokenHelper)
    {
        $this->securityContext = $securityContext;
        $this->em = $em;
        $this->idTokenHelper = $idTokenHelper;
        $this->tokenRepository = $this->em->getRepository("WaldoOpenIdConnectModelBundle:Token");
    }

    public function handle(Request $request)
    {
        try {

            /* @var $token Token */
            $token = $this->isValidRequest($request);

        } catch (TokenRequestException $ex) {

            return new JsonResponse(array(
                'error' => $ex->getError(),
                'error_description' => $ex->getMessage()
                    ), Response::HTTP_FOUND);
        }

        $accessToken = TokenCodeGenerator::generateUniqueCode(
                        $this->tokenRepository, 'accessToken', true
        );
        $refreshToken = TokenCodeGenerator::generateUniqueCode(
                        $this->tokenRepository, 'refreshToken', true
        );

        $token->setCodeToken(null)
                ->setAccessToken($accessToken)
                ->setRefreshToken($refreshToken);

        $this->em->persist($token);
        $this->em->flush();

        $responseParam = array(
            "access_token" => $accessToken,
            "token_type" => "Bearer",
            "refresh_token" => $refreshToken,
            "expires_in" => 3600,
            "id_token" => $this->idTokenHelper->makeIdToken($token)
        );

        $header = array(
            "Cache-Control" => "no-store",
            "Pragma" => "no-cache"
        );

        return new JsonResponse($responseParam, Response::HTTP_OK, $header);
    }

    /**
     * 
     * @param Request $request
     * @return Waldo\OpenIdConnect\ModelBundle\Entity\Token
     * @throws TokenRequestException
     */
    protected function isValidRequest(Request $request)
    {
        if ($this->securityContext->getToken() == null) {
            throw new TokenRequestException('invalid client', 'invalid_request');
        }

        // Check if user is well authenticated
        if (!$this->securityContext->getToken()->isAuthenticated() || !$this->securityContext->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
            throw new TokenRequestException('invalid client', 'invalid_request');
        }

        /* @var $client \Waldo\OpenIdConnect\ModelBundle\Entity\Client */
        $client = $this->securityContext->getToken()->getUser();


        // Check if request have a redirect_uri
        if (!$request->request->has('redirect_uri')) {
            throw new TokenRequestException('no such redirect_uri', 'invalid_request');
        }

        // Check if redirect_uri is a valide one
        if (!in_array($request->request->get('redirect_uri'), $client->getRedirectUris())) {
            throw new TokenRequestException('no matching redirect_uri', 'invalid_request');
        }

        if (!$request->request->has('grant_type')) {
            throw new TokenRequestException('no such grant_type', 'invalid_request');
        }

        if (!in_array($request->request->get('grant_type'), array('authorization_code', 'refresh_token'))) {
            throw new TokenRequestException(
            $request->request->get('grant_type') . ' is not supported', 'unsupported_grant_type');
        }

        if (!$request->request->has('code') && !$request->request->has('refresh_token')) {
            throw new TokenRequestException('no such code or refresh_token', 'invalid_request');
        }

        if ($request->request->get('grant_type') == 'authorization_code') {
            $requestMethod = "getClientTokenByCode";
            $requestCodeToken = $request->request->get('code');
        } elseif ($request->request->get('grant_type') == 'refresh_token') {
            $requestMethod = "getClientTokenByRefreshToken";
            $requestCodeToken = $request->request->get('refresh_token');
        }

        /* @var $token Token */
        $token = $this->tokenRepository
                ->$requestMethod(
                $client, $requestCodeToken
        );

        if ($token === null) {
            throw new TokenRequestException('no such code', 'invalid_authorization_code');
        }

        // Check if redirect_uri is the same as the creation of code token
        if ($request->request->get('redirect_uri') !== $token->getRedirectUri()) {
            throw new TokenRequestException('no matching request_uri', 'invalid_request');
        }

        return $token;
    }

}
