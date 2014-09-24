<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintAccount extends Constraint
{

    public $existingUsername = "This username is already in use, please choose another one.";
    public $existingEmail = "This email is already in use.";

    public function validatedBy()
    {
        return "constraint_account";
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}
