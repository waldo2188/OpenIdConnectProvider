<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintAccountPassword extends Constraint
{
    public $usernameSameAsPassword = "Please, don't use your username as password.";
    public $usernameInPassword = "Please, don't use your username in your password.";
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
