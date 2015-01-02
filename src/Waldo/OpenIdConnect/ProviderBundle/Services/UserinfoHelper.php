<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ProviderBundle\Services\AbstractTokenHelper;
use Waldo\OpenIdConnect\ProviderBundle\Extension\UserinfoExtension;
use Symfony\Component\DependencyInjection\Container;


/**
 * UserinfoHelper
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UserinfoHelper extends AbstractTokenHelper
{
    
    /**
     * @var UserinfoExtension
     */
    protected $userinfoExtension;
    
    public function setUserinfoExtension(UserInfoExtension $userinfoExtension)
    {
        $this->userinfoExtension = $userinfoExtension;
        return $this;
    }
    
    public function makeUserinfo(Token $token)
    {
        
        $account = $token->getAccount();
        $claimedValues = array("sub" => $this->genererateSub($account->getUsername()));
        


        foreach ($token->getScope() as $scope) {
            $propertyName = 'scope' . ucfirst($scope);
            if(property_exists("Waldo\OpenIdConnect\ModelBundle\Entity\Account", $propertyName)) {
                
                foreach(Account::$$propertyName as $accountProperty) {
                    
                    $method = 'get' . ucfirst($accountProperty);

                    if (($value = $account->$method()) != null) {
                        if($value instanceof \DateTime) {
                            $value = $value->getTimestamp();
                        }
                        $claimedValues[Container::underscore($accountProperty)] = $value;
                    }
                }
                
            }
        }

        if($this->userinfoExtension !== null) {
            $claims = $this->userinfoExtension->run($token);
            $claimedValues = array_merge($claimedValues, $claims);
        }

        if($token->getClient()->getUserinfoSignedResponseAlg() !== null) {
            return $this->sign(
                    $token,
                    $token->getClient()->getUserinfoSignedResponseAlg(),
                    $claimedValues
                    );
        }       

        return $claimedValues;
    }
}
