<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ProviderBundle\Exception\UserinfoException;
use Waldo\OpenIdConnect\ProviderBundle\Services\UserinfoHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;

/**
 * UserinfoEndpoint
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UserinfoEndpoint
{

    /**
     * @var EntityManager 
     */
    protected $em;
    
    /**
     * @var UserinfoHelper 
     */
    protected $userinfoHelper;
    

    public function __construct(EntityManager $em, UserinfoHelper $userinfoHelper)
    {
        $this->em = $em;
        $this->userinfoHelper = $userinfoHelper;
    }

    
    public function handle(Request $request)
    {
        try {
            /* @var $token Token */
            $token = $this->isValid($request);
            $token->setAccessToken(null);
            
            $this->em->persist($token);
            $this->em->flush();
            
            $responseParam = $this->userinfoHelper->makeUserinfo($token);

            $header = array(
                "Cache-Control" => "no-store",
                "Pragma" => "no-cache"
            );

            if(is_array($responseParam)) {
                return new JsonResponse($responseParam, Response::HTTP_OK, $header);
            }
            
            $header["Content-Type"] = "application/jwt";
            return new Response($responseParam, Response::HTTP_OK, $header);
            
        } catch (UserinfoException $ex) {
            return new JsonResponse(array(
                'error' => $ex->getError(),
                'error_description' => $ex->getMessage()
            ), Response::HTTP_FOUND);
        }
        
    }
    
    /**
     * 
     * @param Request $request
     * @return Waldo\OpenIdConnect\ModelBundle\Entity\Token
     * @throws UserinfoException
     */
    protected function isValid(Request $request)
    {
        if(!$request->headers->has("authorization")) {
            throw new UserinfoException("no such authorization", "invalid_request");
        }
        
        if(stripos($request->headers->get("authorization"), 'Bearer') === FALSE) {
            throw new UserinfoException("no such bearer code", "invalid_token");
        }
        
        preg_match("/^Bearer (.*)$/", $request->headers->get("authorization"), $matches);
        
        if(count($matches) != 2) {
            throw new UserinfoException("invalid bearer code", "invalid_token");
        }
        
        /* @var $token Token */
        $token = $this->em->getRepository("Waldo\OpenIdConnect\ModelBundle\Entity\Token")
                ->findOneByAccessToken($matches[1]);
        
        if($token === null) {
            throw new UserinfoException("invalid bearer code", "access_denied");
        }
        
        return $token;
    }
    
}
