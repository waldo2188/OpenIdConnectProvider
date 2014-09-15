<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Account;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication;

/**
 * ScopeUtils
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ScopeUtils
{

    /**
     * Extract user's parameter in relation with claimed scope
     * 
     * @param Account $user
     * @param Authentication $authentication
     */
    public function getUserinfoForScopes(Account $user, Authentication $authentication)
    {
        $claims = array();
        
        foreach($authentication->getScope() as $scope) {
            
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
            

            foreach($parametersNeeded as $param) {
                $method = 'get' . ucfirst($param);
                if(($value = $user->$method()) !== null) {
                    $claims[$param] = $value;
                }
            }
            
        }
        
        return $claims;
    }
    
}
