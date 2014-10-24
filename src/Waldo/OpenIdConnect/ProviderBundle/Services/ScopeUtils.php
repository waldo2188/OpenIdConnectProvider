<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Entity\Client;
use Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication;
use Doctrine\ORM\EntityManager;

/**
 * ScopeUtils
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ScopeUtils
{

    /**
     * @var EntityManager 
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Extract user's parameter in relation with claimed scope
     * 
     * @param Account $user
     * @param Authentication $authentication
     */
    public function getUserinfoForScopes(Account $user, Authentication $authentication)
    {
        $claims = array();

        foreach ($authentication->getScope() as $scope) {

            $parametersNeeded = array();

            switch ($scope) {
                case Account::SCOPE_PROFILE :
                    $parametersNeeded = Account::$scopeProfile;
                    break;
                case Account::SCOPE_EMAIL :
                    $parametersNeeded = Account::$scopeEmail;
                    break;
                case Account::SCOPE_PHONE :
                    $parametersNeeded = Account::$scopePhone;
                    break;
                case Account::SCOPE_ADDRESS :
                    $parametersNeeded = Account::$scopeAddress;
                    break;
            }

            foreach ($parametersNeeded as $param) {
                $method = 'get' . ucfirst($param);
                if (($value = $user->$method()) !== null) {
                    $claims[$param] = $value;
                }
            }
        }

        return $claims;
    }

    /**
     * Return true if Enduser have to valid client's Scope
     * 
     * @param Account $user
     * @param Client $client
     * @param Authentication $authentication
     * @return boolean
     */
    public function needToValideScope(Account $user, Authentication $authentication)
    {
        $client = $this->em->getRepository("WaldoOpenIdConnectModelBundle:Client")
                ->findOneByClientId($authentication->getClientId());
        
        $token = $this->em->getRepository("WaldoOpenIdConnectModelBundle:Token")
                ->findOneBy(array(
            "client" => $client->getId(),
            "account" => $user->getId(),
        ));

        if($token === null) {
            return true;
        }
        
        $clientScopes = $client->getScope();
        $clientScopes[] = 'openid';
        
        return !($this->hasSameScope($token->getScope(), $authentication->getScope())
                && $this->hasSameScope($token->getScope(), $clientScopes));
    }

    private function hasSameScope($scopeRef, $scopeCompare)
    {
        if (count($scopeRef) != count($scopeCompare)) {
            return false;
        }

        $itemCheck = 0;
        foreach ($scopeRef as $value) {
            if (!in_array($value, $scopeCompare)) {
                return false;
            }
            $itemCheck++;
        }

        if ($itemCheck != count($scopeCompare)) {
            return false;
        }

        return true;
    }

}
