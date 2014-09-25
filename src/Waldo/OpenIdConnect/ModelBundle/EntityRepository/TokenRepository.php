<?php

namespace Waldo\OpenIdConnect\ModelBundle\EntityRepository;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ModelBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;

class TokenRepository extends EntityRepository
{

    /**
     * Save a relation between a client and an account
     * 
     * @param string $accountId
     * @param string $clientId
     * @param string $code
     * @param array $scope
     * @param string $nonce
     * @param string $redirectUri
     * @return Waldo\OpenIdConnect\ModelBundle\Entity\Token
     */
    public function setCode($accountId, $clientId, $code, $scope, $nonce, $redirectUri)
    {
        $qb = $this->createQueryBuilder("Token");
        $qb->innerJoin("Token.client", "Client")
        ->innerJoin("Token.account", "Account")
                ->where($qb->expr()->andX(
                        $qb->expr()->eq("Client.clientId", ":clientId"),
                        $qb->expr()->eq("Account.id", ":accountId")
                        ))
        ->setParameter("clientId", $clientId)
        ->setParameter("accountId", $accountId)
        ;
        
        $token = $qb->getQuery()->getResult();
        
        if($token !== null && count($token) > 0) {
            $token = $token[0];
        } else {
            $token = new Token();
            $token->setAccount($this->_em->getRepository("Waldo\OpenIdConnect\ModelBundle\Entity\Account")
                    ->findOneById($accountId))
                    ->setClient($this->_em->getRepository("Waldo\OpenIdConnect\ModelBundle\Entity\Client")
                    ->findOneByClientId($clientId));
        }
        
        $token->setCodeToken($code)
                ->setScope($scope)
                ->setNonce($nonce)
                ->setRedirectUri($redirectUri);
        
        $this->_em->persist($token);
        $this->_em->flush();
        
        return $token;
    }
    
    
    /**
     * Check if the code token is valid for a client
     * 
     * @param Client $client
     * @param type $code
     * @return boolean
     */
    public function getClientTokenByCode(Client $client, $code)
    {
        return $this->getClientToken($client, $code, 'codeToken');
    }
    
    /**
     * Check if the code token is valid for a client
     * 
     * @param Client $client
     * @param type $refreshToken
     * @return boolean
     */
    public function getClientTokenByRefreshToken(Client $client, $refreshToken)
    {
        return $this->getClientToken($client, $refreshToken, 'refreshToken');
    }
    
    protected function getClientToken(Client $client, $token, $type)
    {
        $qb = $this->createQueryBuilder("Token");
            
        $qb->select("Token")
                ->where($qb->expr()->andX(
                        $qb->expr()->eq("Token.client", ":client"),
                        $qb->expr()->eq("Token." . $type, ":" . $type)
                        ))
                ->setParameter("client", $client)
                ->setParameter($type, $token)
                ;
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    

}
