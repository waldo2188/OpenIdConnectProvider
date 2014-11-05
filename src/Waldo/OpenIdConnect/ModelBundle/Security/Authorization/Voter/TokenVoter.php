<?php

namespace Waldo\OpenIdConnect\ModelBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of TokenVoter
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class TokenVoter implements VoterInterface
{
    
    const REVOKE_AUTHORIZATION = "revoke_authorization";
    
    public function supportsAttribute($attribute)
    {
        return $attribute === self::REVOKE_AUTHORIZATION;
    }

    public function supportsClass($class)
    {
        $supportedClass = 'Waldo\OpenIdConnect\ModelBundle\Entity\Token';

        return $supportedClass === $class || $class instanceof $supportedClass;
    }

    /**
     * 
     * @param TokenInterface $token
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\Token $object
     * @param array $attributes
     * @return type
     * @throws \InvalidArgumentException
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {       
        // check if class of this object is supported by this voter
        if (!$this->supportsClass($object)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if(1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for REVOKE_AUTHORIZATION'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        if(self::REVOKE_AUTHORIZATION === $attribute) {
            if($user->getId() === $object->getAccount()->getId() || in_array('ROLE_ADMIN', $user->getRoles())) {
                return VoterInterface::ACCESS_GRANTED;
            }
            
            return VoterInterface::ACCESS_DENIED;
        }
        
        return VoterInterface::ACCESS_ABSTAIN;
    }
}
