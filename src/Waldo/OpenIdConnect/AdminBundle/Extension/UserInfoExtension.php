<?php

namespace Waldo\OpenIdConnect\AdminBundle\Extension;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ProviderBundle\Extension\UserinfoExtensionInterface;
use Waldo\OpenIdConnect\ModelBundle\Expression\UserModel;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Description of EnduserInfo
 *
 */
class UserInfoExtension implements UserinfoExtensionInterface
{
    
    public function handle(Token $token)
    {
        if(!in_array('roles', $token->getScope())) {
            return null;
        }
                
        $roles = array();
        $expression = new ExpressionLanguage();
        
        /* @var $rule \Waldo\OpenIdConnect\ModelBundle\Entity\UserRolesRules */
        foreach($token->getClient()->getUserRolesRulesList() as $rule) {
            
            if($rule->isEnabled()) {
                $userModel = (array) new UserModel($token->getAccount());

                if($expression->evaluate($rule->getExpression(), $userModel)) {
                    $roles = array_merge($roles, $rule->getRoles());
                }
            }
        }

        return $roles ? array('roles' => $roles) : null;
    }
    
}
