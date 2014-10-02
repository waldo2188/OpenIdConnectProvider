<?php

namespace Waldo\OpenIdConnect\ModelBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;

/**
 * AccountVoter
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AccountVoter implements VoterInterface
{

    const DISABLED = 'disabled';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::DISABLED
        ));
    }

    public function supportsClass($class)
    {
        return $class instanceof Account;
    }

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
                'Only one attribute is allowed for VIEW or EDIT'
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
        
        
        if(self::DISABLED === $attribute) {
            if($user->getId() === $object->getId()) {
                return VoterInterface::ACCESS_DENIED;
            }
            return VoterInterface::ACCESS_GRANTED;
        }
        
        return VoterInterface::ACCESS_ABSTAIN;
    }

}
